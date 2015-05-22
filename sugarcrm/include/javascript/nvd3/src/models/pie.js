nv.models.pie = function() {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var margin = {top: 0, right: 0, bottom: 0, left: 0},
      width = 500,
      height = 500,
      getValues = function(d) { return d; },
      getX = function(d) { return d.key; },
      getY = function(d) { return d.value; },
      getDescription = function(d) { return d.description; },
      id = Math.floor(Math.random() * 10000), //Create semi-unique ID in case user doesn't select one
      valueFormat = d3.format(',.2f'),
      showLabels = true,
      showLeaders = true,
      pieLabelsOutside = true,
      donutLabelsOutside = true,
      labelThreshold = 0.01, //if slice percentage is under this, don't show label
      donut = false,
      hole = false,
      labelSunbeamLayout = false,
      leaderLength = 20,
      textOffset = 5,
      startAngle = function(d) { return d.startAngle; },
      endAngle = function(d) { return d.endAngle; },
      donutRatio = 0.447,
      durationMs = 0,
      direction = 'ltr',
      color = function(d, i) { return nv.utils.defaultColor()(d, d.series); },
      fill = color,
      classes = function(d, i) { return 'nv-slice nv-series-' + d.series; },
      dispatch = d3.dispatch('chartClick', 'elementClick', 'elementDblClick', 'elementMouseover', 'elementMouseout', 'elementMousemove');

  //============================================================


  function chart(selection) {
    selection.each(function(data) {

      var availableWidth = width - margin.left - margin.right,
          availableHeight = height - margin.top - margin.bottom,
          container = d3.select(this);

      // Setup the Pie chart and choose the data element
      var pie = d3.layout.pie()
            .sort(null)
            .value(function(d) { return d.disabled ? 0 : getY(d); });

      //------------------------------------------------------------
      // recalculate width and height based on label length
      var labelLengths = [];
      if (showLabels && pieLabelsOutside) {
        labelLengths = nv.utils.stringSetLengths(
            data.map(function(d) { return d.key; }),
            container,
            function(d) { return d; }
          );
      }

      //------------------------------------------------------------
      // Setup containers and skeleton of chart
      var wrap = container.selectAll('.nv-wrap.nv-pie').data([data]);
      var wrapEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-pie nv-chart-' + id);
      var defsEnter = wrapEnter.append('defs');
      var gEnter = wrapEnter.append('g');
      var g = wrap.select('g');

      //set up the gradient constructor function
      chart.gradient = function(d, i) {
        var params = {x: 0, y: 0, r: pieRadius, s: (donut ? (donutRatio * 100) + '%' : '0%'), u: 'userSpaceOnUse'};
        return nv.utils.colorRadialGradient(d, id + '-' + i, params, color(d, i), wrap.select('defs'));
      };

      gEnter.append('g').attr('class', 'nv-pie');
      var pieWrap = g.select('.nv-pie');
      gEnter.append('g').attr('class', 'nv-holeWrap');
      var holeWrap = g.select('.nv-holeWrap');

      wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');
      pieWrap.attr('transform', 'translate(' + availableWidth / 2 + ',' + availableHeight / 2 + ')');

      //------------------------------------------------------------

      container
        .on('click', function(d, i) {
          dispatch.chartClick({
            data: d,
            index: i,
            pos: d3.event,
            id: id
          });
        });

      var slices = wrap.select('.nv-pie').selectAll('.nv-slice')
            .data(pie);

      slices.exit().remove();

      function buildEventObject(e, d, i) {
        return {
            label: getX(d.data),
            value: getY(d.data),
            point: d.data,
            pointIndex: i,
            pos: [e.offsetX, e.offsetY],
            id: id,
            e: e
          };
      }

      var ae = slices.enter().append('g')
            .on('mouseover', function(d, i) {
              d3.select(this).classed('hover', true);
              var eo = buildEventObject(d3.event, d, i);
              dispatch.elementMouseover(eo);
            })
            .on('mousemove', function(d, i) {
              var eo = buildEventObject(d3.event, d, i);
              dispatch.elementMousemove(eo);
            })
            .on('mouseout', function(d, i) {
              d3.select(this).classed('hover', false);
              dispatch.elementMouseout();
            })
            .on('click', function(d, i) {
              d3.event.stopPropagation();
              var eo = buildEventObject(d3.event, d, i);
              dispatch.elementClick(eo);
            })
            .on('dblclick', function(d, i) {
              d3.event.stopPropagation();
              var eo = buildEventObject(d3.event, d, i);
              dispatch.elementDblClick(eo);
            });

          ae.append('path')
              .style('stroke', '#ffffff')
              .style('stroke-width', 3)
              .style('stroke-opacity', 0)
              .each(function(d, i) {
                this._current = d;
              });

          ae.append('g')
              .attr('transform', 'translate(0,0)')
              .attr('class', 'nv-label');

          ae.select('.nv-label')
              .append('rect')
              .style('fill-opacity', 0)
              .style('stroke-opacity', 0);
          ae.select('.nv-label')
              .append('text')
              .style('fill-opacity', 0);

          ae.append('polyline')
              .attr('class', 'nv-label-leader')
              .style('stroke-opacity', 0);

      // Donut Hole Text
      //------------------------------------------------------------

      if (hole && donut) {
        holeWrap.selectAll('text').remove();
        holeWrap.append('text')
          .attr('text-anchor', 'middle')
          .attr('class', 'nv-pie-hole-value')
          .attr('dy', '.35em')
          .style('fill', '#333')
          .style('font-size', '32px')
          .style('font-weight', 'bold');
        holeWrap.select('text')
          .text(function(d) { return hole(d, this); });
        var holeBBox = holeWrap.node().getBBox();
        availableHeight -= holeBBox.height + holeBBox.y;
      }

      // UPDATE
      //------------------------------------------------------------

      var maxWidthRadius = availableWidth / 2,
          maxHeightRadius = availableHeight / 2,
          extHeight = [];

      // adjust max height radius for start/end angles
      slices.select('path')
        .each(function(d, i) {
          var start = startAngle(d),
              end = endAngle(d),
              N = start <= 0 && end >= 0,
              S = start <= Math.PI && end >= Math.PI,
              cosStart = Math.cos(start),
              cosEnd = Math.cos(end);
          if (N) {
            extHeight.push(1);
          }
          if (S) {
            extHeight.push(-1);
          }
          extHeight.push(cosStart);
          extHeight.push(cosEnd);
        });
      extHeight = d3.extent(extHeight);

      // scale up height radius to fill extents
      maxHeightRadius *= 2 / (Math.abs(extHeight[0]) + Math.abs(extHeight[1]));

      // reduce width radius for width of labels
      if (showLabels && pieLabelsOutside) {
        var widthRadii = [maxWidthRadius],
            heightRadii = [maxHeightRadius];

        slices.select('path')
          .each(function(d, i) {
            if (!labelOpacity(d)) {
              return;
            }
            var theta = (startAngle(d) + endAngle(d)) / 2,
                sin = Math.abs(Math.sin(theta)),
                cos = Math.abs(Math.cos(theta)),
                bW = maxWidthRadius - leaderLength - textOffset - labelLengths[i],
                bH = maxHeightRadius - 7,
                rW = sin ? bW / sin : bW, //don't divide by zero, fool
                rH = cos ? bH / cos : bH;
            widthRadii.push(rW);
            heightRadii.push(rH);
          });

        maxWidthRadius = d3.min(widthRadii);
        maxHeightRadius = d3.min(heightRadii);
      }

      var labelRadius = Math.min(maxWidthRadius, maxHeightRadius),
          pieRadius = labelRadius - (showLabels && pieLabelsOutside ? leaderLength : 0),
          offsetVertical = availableHeight / 2;

      if (maxHeightRadius > availableHeight / 2) {
          offsetVertical += labelRadius - labelRadius / 2;
      }

      pieWrap.attr('transform', 'translate(' + availableWidth / 2 + ',' + offsetVertical + ')');

      if (hole && donut) {
        holeWrap
          .attr('transform', 'translate(' + availableWidth / 2 + ',' + offsetVertical + ')');
      }

      var pieArc = d3.svg.arc()
            .innerRadius(0)
            .outerRadius(pieRadius)
            .startAngle(startAngle)
            .endAngle(endAngle);

      if (donut) {
        pieArc.innerRadius(pieRadius * donutRatio);
      }

      var labelArc = d3.svg.arc()
            .innerRadius(0)
            .outerRadius(pieRadius)
            .startAngle(startAngle)
            .endAngle(endAngle);

      if (pieLabelsOutside) {
        if (!donut || donutLabelsOutside) {
          labelArc
            .innerRadius(labelRadius)
            .outerRadius(labelRadius);
        } else {
          labelArc
            .outerRadius(pieRadius * donutRatio);
        }
      }

      slices
        .classed('nv-active', function(d) { return d.data.active === 'active'; })
        .classed('nv-inactive', function(d) { return d.data.active === 'inactive'; })
        .attr('class', function(d) { return classes(d.data, d.data.series); })
        .attr('fill', function(d) { return fill(d.data, d.data.series); });

      // removed d3 transition in MACAROON-133 because
      // there is a "Maximum call stack size exceeded at Date.toString" error
      // in PMSE that stops d3 from calling transitions
      // this may be a logger issue or some recursion somewhere in PMSE
      // slices.select('path').transition().duration(durationMs)
      //   .attr('d', arc)
      //   .attrTween('d', arcTween);

      slices.select('path')
        .attr('d', pieArc)
        .style('stroke-opacity', function(d) {
          return startAngle(d) === endAngle(d) ? 0 : 1;
        });

      if (showLabels) {
        // This does the normal label
        slices.select('.nv-label')
          .attr('transform', function(d) {
            if (labelSunbeamLayout) {
              d.outerRadius = pieRadius + 10; // Set Outer Coordinate
              d.innerRadius = pieRadius + 15; // Set Inner Coordinate
              var rotateAngle = (startAngle(d) + endAngle(d)) / 2 * (180 / Math.PI);
              rotateAngle += 90 * alignedRight(d, labelArc);
              return 'translate(' + labelArc.centroid(d) + ') rotate(' + rotateAngle + ')';
            } else {
              var labelsPosition = labelArc.centroid(d),
                  leadOffset = showLeaders ? (leaderLength + textOffset) * alignedRight(d, labelArc) : 0;
              return 'translate(' + [labelsPosition[0] + leadOffset, labelsPosition[1]] + ')';
            }
          });

        slices.select('.nv-label text')
          .text(function(d) {
            return labelOpacity(d) ? getX(d.data) : '';
          })
          .attr('dy', '.35em')
          .style('fill', '#555')
          .style('fill-opacity', labelOpacity)
          .style('text-anchor', function(d) {
            //center the text on it's origin or begin/end if orthogonal aligned
            //labelSunbeamLayout ? ((d.startAngle + d.endAngle) / 2 < Math.PI ? 'start' : 'end') : 'middle'
            var anchor = alignedRight(d, labelArc) === 1 ? 'start' : 'end';
            if (!pieLabelsOutside) {
              anchor = 'middle';
            }
            anchor = direction === 'rtl' ? anchor === 'start' ? 'end' : 'start' : anchor;
            return anchor;
          });

        if (!pieLabelsOutside) {
          slices.select('.nv-label')
            .each(function(d) {
              if (!labelOpacity(d)) {
                return;
              }
              var slice = d3.select(this),
                  textBox = slice.select('text').node().getBBox();
              slice.select('rect')
                .attr('rx', 3)
                .attr('ry', 3)
                .attr('width', textBox.width + 10)
                .attr('height', textBox.height + 10)
                .attr('transform', function() {
                  return 'translate(' + [textBox.x - 5, textBox.y - 5] + ')';
                })
                .style('fill', '#fff')
                .style('fill-opacity', labelOpacity);
            });
        } else if (showLeaders) {
          slices.select('.nv-label-leader')
            .attr('points', function(d) {
              if (!labelOpacity(d)) {
                return '0,0';
              }
              var outerArc = d3.svg.arc()
                    .innerRadius(pieRadius)
                    .outerRadius(pieRadius)
                    .startAngle(startAngle)
                    .endAngle(endAngle);
              var leadOffset = showLeaders ? leaderLength * alignedRight(d, outerArc) : 0,
                  outerArcPoints = outerArc.centroid(d),
                  labelArcPoints = labelArc.centroid(d),
                  leadArcPoints = [labelArcPoints[0] + leadOffset, labelArcPoints[1]];
              return outerArcPoints + ' ' + labelArcPoints + ' ' + leadArcPoints;
            })
            .style('stroke', '#aaa')
            .style('fill', 'none')
            .style('stroke-opacity', labelOpacity);
        }
      } else {
        slices.select('.nv-label-leader').style('stroke-opacity', 0);
        slices.select('.nv-label rect').style('fill-opacity', 0);
        slices.select('.nv-label text').style('fill-opacity', 0);
      }

      // Utility Methods
      //------------------------------------------------------------

      function labelOpacity(d) {
        var percent = (endAngle(d) - startAngle(d)) / (2 * Math.PI);
        return percent > labelThreshold ? 1 : 0;
      }

      function alignedRight(d, arc) {
        var circ = Math.PI * 2,
            midArc = ((startAngle(d) + endAngle(d)) / 2 + circ) % circ;
        return midArc > 0 && midArc < Math.PI ? 1 : -1;
      }

      function arcTween(d) {
        if (!donut) {
          d.innerRadius = 0;
        }
        var i = d3.interpolate(this._current, d);
        this._current = i(0);

        return function(t) {
          var iData = i(t);
          return pieArc(iData);
        };
      }

      function tweenPie(b) {
        b.innerRadius = 0;
        var i = d3.interpolate({startAngle: 0, endAngle: 0}, b);
        return function(t) {
          return pieArc(i(t));
        };
      }

    });

    return chart;
  }


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  chart.dispatch = dispatch;

  chart.color = function(_) {
    if (!arguments.length) {
      return color;
    }
    color = _;
    return chart;
  };
  chart.fill = function(_) {
    if (!arguments.length) {
      return fill;
    }
    fill = _;
    return chart;
  };
  chart.classes = function(_) {
    if (!arguments.length) {
      return classes;
    }
    classes = _;
    return chart;
  };
  chart.gradient = function(_) {
    if (!arguments.length) {
      return gradient;
    }
    gradient = _;
    return chart;
  };

  chart.margin = function(_) {
    if (!arguments.length) {
      return margin;
    }
    margin.top    = typeof _.top    != 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  != 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom != 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   != 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.width = function(_) {
    if (!arguments.length) {
      return width;
    }
    width = _;
    return chart;
  };

  chart.height = function(_) {
    if (!arguments.length) {
      return height;
    }
    height = _;
    return chart;
  };

  chart.values = function(_) {
    if (!arguments.length) {
      return getValues;
    }
    getValues = _;
    return chart;
  };

  chart.x = function(_) {
    if (!arguments.length) {
      return getX;
    }
    getX = _;
    return chart;
  };

  chart.y = function(_) {
    if (!arguments.length) {
      return getY;
    }
    getY = d3.functor(_);
    return chart;
  };

  chart.description = function(_) {
    if (!arguments.length) {
      return getDescription;
    }
    getDescription = _;
    return chart;
  };

  chart.showLabels = function(_) {
    if (!arguments.length) {
      return showLabels;
    }
    showLabels = _;
    return chart;
  };

  chart.labelSunbeamLayout = function(_) {
    if (!arguments.length) {
      return labelSunbeamLayout;
    }
    labelSunbeamLayout = _;
    return chart;
  };

  chart.donutLabelsOutside = function(_) {
    if (!arguments.length) {
      return donutLabelsOutside;
    }
    donutLabelsOutside = _;
    return chart;
  };

  chart.pieLabelsOutside = function(_) {
    if (!arguments.length) {
      return pieLabelsOutside;
    }
    pieLabelsOutside = _;
    return chart;
  };

  chart.showLeaders = function(_) {
    if (!arguments.length) {
      return showLeaders;
    }
    showLeaders = _;
    return chart;
  };

  chart.donut = function(_) {
    if (!arguments.length) {
      return donut;
    }
    donut = _;
    return chart;
  };

  chart.hole = function(_) {
    if (!arguments.length) {
      return hole;
    }
    hole = d3.functor(_);
    return chart;
  };

  chart.donutRatio = function(_) {
    if (!arguments.length) {
      return donutRatio;
    }
    donutRatio = _;
    return chart;
  };

  chart.startAngle = function(_) {
    if (!arguments.length) {
      return startAngle;
    }
    startAngle = _;
    return chart;
  };

  chart.endAngle = function(_) {
    if (!arguments.length) {
      return endAngle;
    }
    endAngle = _;
    return chart;
  };

  chart.id = function(_) {
    if (!arguments.length) {
      return id;
    }
    id = _;
    return chart;
  };

  chart.valueFormat = function(_) {
    if (!arguments.length) {
      return valueFormat;
    }
    valueFormat = _;
    return chart;
  };

  chart.labelThreshold = function(_) {
    if (!arguments.length) {
      return labelThreshold;
    }
    labelThreshold = _;
    return chart;
  };

  chart.direction = function(_) {
    if (!arguments.length) {
      return direction;
    }
    direction = _;
    return chart;
  };

  //============================================================

  return chart;
}
