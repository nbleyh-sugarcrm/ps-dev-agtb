(function($){
    /**
   	 * Custom sort method for numbers.  Set sSortDataType:"dom-number" to use
   	 */
   	$.fn.dataTableExt.afnSortData['dom-number'] = function  ( oSettings, iColumn )
   	{
   		var aData = [];
        var sortString;
        //Use JQuery select on the table cell which has a span with an sfuuid attribute
        $('td:eq('+iColumn+') span[sfuuid]', oSettings.oApi._fnGetTrNodes(oSettings) ).each( function () {
            // look for text inside a span of class numberValue,
            // otherwise fall back to full text content
            sortString = $(this).find('.numberValue').text();
            if(_.isEmpty(sortString)) {
                sortString = this.textContent;
            }
            aData.push(SUGAR.App.currency.unformatAmount(sortString, SUGAR.App.user.get('number_grouping_separator'), SUGAR.App.user.get('decimal_separator'), false));
        });
   		return aData;
   	}
})(jQuery);