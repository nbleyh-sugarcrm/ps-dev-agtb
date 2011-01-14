<?php
/*********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
$config['builds']['com']['flav'] = array('com');
$config['builds']['com']['lic'] = array('gpl');
$config['blackList']['com'] = array(
'sugarcrm/modules/Contracts'=>1,
'sugarcrm/modules/ContractTypes'=>1,
'sugarcrm/modules/Expressions'=>1,
'sugarcrm/modules/Forecasts'=>1,
'sugarcrm/modules/ForecastSchedule'=>1,
'sugarcrm/modules/KBContents'=>1,
'sugarcrm/modules/KBDocumentKBTags'=>1,
'sugarcrm/modules/KBDocumentRevisions'=>1,
'sugarcrm/modules/KBDocuments'=>1,
'sugarcrm/modules/KBTags'=>1,
'sugarcrm/modules/Manufacturers'=>1,
'sugarcrm/modules/ProductBundleNotes'=>1,
'sugarcrm/modules/ProductBundles'=>1,
'sugarcrm/modules/ProductCategories'=>1,
'sugarcrm/modules/Products'=>1,
'sugarcrm/modules/ProductTemplates'=>1,
'sugarcrm/modules/ProductTypes'=>1,
'sugarcrm/modules/Quotas'=>1,
'sugarcrm/modules/Quotes'=>1,
'sugarcrm/modules/Reports'=>1,
'sugarcrm/modules/Shippers'=>1,
'sugarcrm/modules/TaxRates'=>1,
'sugarcrm/modules/TeamNotices'=>1,
'sugarcrm/modules/Teams'=>1,
'sugarcrm/modules/TimePeriods'=>1,
'sugarcrm/modules/Trackers/Dashlets'=>1,
'sugarcrm/modules/WorkFlow'=>1,
'sugarcrm/modules/WorkFlowActions'=>1,
'sugarcrm/modules/WorkFlowActionShells'=>1,
'sugarcrm/modules/WorkFlowAlerts'=>1,
'sugarcrm/modules/WorkFlowAlertShells'=>1,
'sugarcrm/modules/WorkFlowTriggerShells'=>1,
'sugarcrm/modules/ACLFields'=>1,
'sugarcrm/modules/ProjectResources'=>1,
'sugarcrm/modules/Holidays'=>1,
'sugarcrm/service/v3_1'=>1,
'sugarcrm/modules/Connectors/connectors/filters/ext/rest/zoominfocompany'=>1,
'sugarcrm/modules/Connectors/connectors/filters/ext/rest/zoominfoperson'=>1,
'sugarcrm/modules/Connectors/connectors/filters/ext/soap/hoovers'=>1,
'sugarcrm/modules/Connectors/connectors/filters/ext/soap/jigsaw'=>1,
'sugarcrm/modules/Connectors/connectors/sources/ext/rest/zoominfocompany'=>1,
'sugarcrm/modules/Connectors/connectors/sources/ext/rest/zoominfoperson'=>1,
'sugarcrm/modules/Connectors/connectors/sources/ext/soap/hoovers'=>1,
'sugarcrm/modules/Connectors/connectors/sources/ext/soap/jigsaw'=>1,
'sugarcrm/include/workflow'=>1,
'sugarcrm/include/SugarFields/Fields/Teamset'=>1,
'sugarcrm/modules/Charts/Dashlets/MyOpportunitiesGaugeDashlet'=>1,
'sugarcrm/modules/Charts/Dashlets/MyForecastingChartDashlet'=>1,
'sugarcrm/modules/Sync'=>1,
'sugarcrm/portal'=>1,
'sugarcrm/include/SugarWireless'=>1,
'sugarcrm/include/SugarCharts/swf'=>1,


'sugarcrm/modules/CustomQueries'=>1,
'sugarcrm/modules/DataSets'=>1,
'sugarcrm/modules/ReportMaker'=>1,

'sugarcrm/include/DashletContainer'=>1,
'sugarcrm/include/SugarPlugins'=>1,

'sugarcrm/include/SugarObjects/templates/basic/icons/basic_bar_32.png'=>1,
'sugarcrm/include/SugarObjects/templates/company/icons/company_bar_32.png'=>1,
'sugarcrm/include/SugarObjects/templates/file/icons/file_bar_32.png'=>1,
'sugarcrm/include/SugarObjects/templates/issue/icons/issue_bar_32.png'=>1,
'sugarcrm/include/SugarObjects/templates/person/icons/person_bar_32.png'=>1,
'sugarcrm/include/SugarObjects/templates/sale/icons/sale_bar_32.png'=>1,

'sugarcrm/modules/Notifications'=>1,
'sugarcrm/themes/default/images/icon_notifications.gif'=>1,
'sugarcrm/themes/default/images/icon_notifications.png'=>1,
'sugarcrm/themes/default/images/Notifications.gif'=>1,

'sugarcrm/modules/SugarFollowing'=>1,
'sugarcrm/themes/default/images/user_follow.png'=>1,    
'sugarcrm/themes/default/images/user_unfollow.png'=>1,

'sugarcrm/include/EditView/InlineEdit.css'=>1,
'sugarcrm/include/EditView/InlineEdit.js'=>1,
'sugarcrm/include/EditView/InlineEdit.php'=>1,
'sugarcrm/include/MVC/View/views/view.inlinefield.php'=>1,
'sugarcrm/include/MVC/View/views/view.inlinefieldsave.php'=>1,

'sugarcrm/modules/SugarFavorites'=>1,
'sugarcrm/themes/default/images/star-sheet.png'=>1,

'sugarcrm/themes/Amore'=>1,
'sugarcrm/themes/Green'=>1,
'sugarcrm/themes/Awesome80s'=>1,
'sugarcrm/themes/BoldMove'=>1,
'sugarcrm/themes/FinalFrontier'=>1,
'sugarcrm/themes/GoldenGate'=>1,
'sugarcrm/themes/Legacy'=>1,
'sugarcrm/themes/Links'=>1,
'sugarcrm/themes/Love'=>1,
'sugarcrm/themes/Paradise'=>1,
'sugarcrm/themes/Retro'=>1,
'sugarcrm/themes/RipCurl'=>1,
'sugarcrm/themes/RipCurlorg'=>1,
'sugarcrm/themes/RTL'=>1,
'sugarcrm/themes/Shred'=>1,
'sugarcrm/themes/Sugar'=>1,
'sugarcrm/themes/Sugar2006'=>1,
'sugarcrm/themes/SugarClassic'=>1,
'sugarcrm/themes/SugarIE6'=>1,
'sugarcrm/themes/SugarLite'=>1,
'sugarcrm/themes/Sunset'=>1,
'sugarcrm/themes/TrailBlazers'=>1,
'sugarcrm/themes/VintageSugar'=>1,
'sugarcrm/themes/WhiteSands'=>1,
'sugarcrm/include/Expressions'=>1,
'sugarcrm/modules/ExpressionEngine'=>1,

'sugarcrm/include/externalAPI/Facebook'=>1,
'sugarcrm/include/externalAPI/Google'=>1,
'sugarcrm/include/externalAPI/GoToMeeting'=>1,
'sugarcrm/include/externalAPI/Twitter'=>1,
'sugarcrm/include/externalAPI/WebEx'=>1,

'sugarcrm/themes/default/images/gmail_logo.png'=>1,
'sugarcrm/themes/default/images/yahoomail_logo.png'=>1,
'sugarcrm/themes/default/images/exchange_logo.png'=>1,

'sugarcrm/modules/DCEActions'=>1,
'sugarcrm/modules/DCEClients'=>1,
'sugarcrm/modules/DCEClusters'=>1,
'sugarcrm/modules/DCEDataBases'=>1,
'sugarcrm/modules/DCEInstances'=>1,
'sugarcrm/modules/DCEReports'=>1,
'sugarcrm/modules/DCETemplates'=>1,
'sugarcrm/modules/Charts/Dashlets/DCEActionsByTypesDashlet'=>1,

'sugarcrm/themes/default/images/dce_settings.gif'=>1,
'sugarcrm/themes/default/images/DCEClusters.gif'=>1,
'sugarcrm/themes/default/images/DCEInstances.gif'=>1,
'sugarcrm/themes/default/images/DCElicensingReport.gif'=>1,
'sugarcrm/themes/default/images/DCETemplates.gif'=>1,
'sugarcrm/themes/default/images/DCEDataBases.gif'=>1,
'sugarcrm/themes/default/images/createDCEClusters.gif'=>1,
'sugarcrm/themes/default/images/createDCEInstances.gif'=>1,
'sugarcrm/themes/default/images/createDCETemplates.gif'=>1,
'sugarcrm/themes/default/images/createDCEDataBases.gif'=>1,
'sugarcrm/themes/default/images/icon_DCEActions_32.gif'=>1,
'sugarcrm/themes/default/images/icon_DCEDataBases_32.gif'=>1,
'sugarcrm/themes/default/images/icon_DCEInstances_32.gif'=>1,
'sugarcrm/themes/default/images/icon_DCEClusters_32.gif'=>1,
'sugarcrm/themes/default/images/icon_DCETemplates_32.gif'=>1,
'sugarcrm/themes/default/images/icon_DCEReports_32.gif'=>1,

'sugarcrm/modules/QueryBuilder'=>1,
'sugarcrm/modules/Queues'=>1,

'sugarcrm/include/images/sugarsales_lg.png'=>1,
'sugarcrm/include/images/sugarsales_lg_dce.png'=>1,
'sugarcrm/include/images/sugarsales_lg_ent.png'=>1,
'sugarcrm/include/images/sugarsales_lg_express.png'=>1,
'sugarcrm/include/images/sugarsales_lg_open.png'=>1,
'sugarcrm/include/images/sugar_md.png'=>1,
'sugarcrm/include/images/sugar_md_dce.png'=>1,
'sugarcrm/include/images/sugar_md_dev.png'=>1,
'sugarcrm/include/images/sugar_md_ent.png'=>1,
'sugarcrm/include/images/sugar_md_express.png'=>1,
'sugarcrm/include/images/sugar_md_sales.png'=>1,

'sugarcrm/themes/default/images/AccountReports.gif'=>1,
'sugarcrm/themes/default/images/CallReports.gif'=>1,
'sugarcrm/themes/default/images/CaseReports.gif'=>1,
'sugarcrm/themes/default/images/ContactReports.gif'=>1,
'sugarcrm/themes/default/images/Contracts.gif'=>1,
'sugarcrm/themes/default/images/ContractReports.gif'=>1,
'sugarcrm/themes/default/images/CreateContracts.gif'=>1,
'sugarcrm/themes/default/images/CreateCustomQuery.gif'=>1,
'sugarcrm/themes/default/images/CreateDataSet.gif'=>1,
'sugarcrm/themes/default/images/CreateProducts.gif'=>1,
'sugarcrm/themes/default/images/CreateReport.gif'=>1,
'sugarcrm/themes/default/images/CreateTimePeriods.gif'=>1,
'sugarcrm/themes/default/images/CreateWorkflowDefinition.gif'=>1,
'sugarcrm/themes/default/images/EmailReports.gif'=>1,
'sugarcrm/themes/default/images/ForecastReports.gif'=>1,
'sugarcrm/themes/default/images/Forecasts.gif'=>1,
'sugarcrm/themes/default/images/ForecastWorksheet.gif'=>1,
'sugarcrm/themes/default/images/Icon_Charts_Funnel.gif'=>1,
'sugarcrm/themes/default/images/icon_Charts_Gauge.gif'=>1,
'sugarcrm/themes/default/images/icon_Contracts.gif'=>1,
'sugarcrm/themes/default/images/icon_FavoriteReports.gif'=>1,
'sugarcrm/themes/default/images/icon_Forecasts.gif'=>1,
'sugarcrm/themes/default/images/icon_Products.gif'=>1,
'sugarcrm/themes/default/images/icon_ProductTemplates.gif'=>1,
'sugarcrm/themes/default/images/icon_Quotes.gif'=>1,
'sugarcrm/themes/default/images/LeadReports.gif'=>1,
'sugarcrm/themes/default/images/MeetingReports.gif'=>1,
'sugarcrm/themes/default/images/MyReports.gif'=>1,
'sugarcrm/themes/default/images/OfflineClient.gif'=>1,
'sugarcrm/themes/default/images/Price_List.gif'=>1,
'sugarcrm/themes/default/images/PriceList.gif'=>1,
'sugarcrm/themes/default/images/Product_Categories.gif'=>1,
'sugarcrm/themes/default/images/Product_Type.gif'=>1,
'sugarcrm/themes/default/images/ProductCategories.gif'=>1,
'sugarcrm/themes/default/images/Products.gif'=>1,
'sugarcrm/themes/default/images/ProductTemplate.gif'=>1,
'sugarcrm/themes/default/images/ProductTypes.gif'=>1,
'sugarcrm/themes/default/images/QuoteReports.gif'=>1,
'sugarcrm/themes/default/images/Quotes.gif'=>1,
'sugarcrm/themes/default/images/ReportMaker.gif'=>1,
'sugarcrm/themes/default/images/Reports.gif'=>1,
'sugarcrm/themes/default/images/TaskReports.gif'=>1,
'sugarcrm/themes/default/images/TaxRates.gif'=>1,
'sugarcrm/themes/default/images/TimePeriods.gif'=>1,
'sugarcrm/themes/default/images/Workflow.gif'=>1,
'sugarcrm/themes/default/images/WorkflowSequence.gif'=>1,
'sugarcrm/themes/default/images/dcMenuIconBarCenter.png'=>1,
'sugarcrm/themes/default/images/dcMenuIconBarCenterHover.png'=>1,
'sugarcrm/themes/default/images/dcMenuIconBarLeft.png'=>1,
'sugarcrm/themes/default/images/dcMenuIconBarRight.png'=>1,
'sugarcrm/themes/default/images/dcMenuRndBtnBg.png'=>1,
'sugarcrm/themes/default/images/dcMenuRndBtnBgHover.png'=>1,
'sugarcrm/themes/default/images/dcMenuSearchBox.png'=>1,
'sugarcrm/themes/default/images/dcMenuSearchBtn.png'=>1,
'sugarcrm/themes/default/images/dcMenuSugarCube.png'=>1,
'sugarcrm/themes/default/images/globalLinksCenter.png'=>1,
'sugarcrm/themes/default/images/globalLinksLeft.png'=>1,
'sugarcrm/themes/default/images/globalLinksToggle.png'=>1,
'sugarcrm/themes/default/images/shortCutsToggle.png'=>1,
'sugarcrm/themes/default/images/slider_button_less.png'=>1,
'sugarcrm/themes/default/images/slider_button_more.png'=>1,
'sugarcrm/themes/default/images/tabDivider.png'=>1,
'sugarcrm/themes/default/images/tabDownArrowHover.png'=>1,
'sugarcrm/themes/default/images/tabDownArrowOff.png'=>1,
'sugarcrm/themes/default/images/tabDownArrowOn.png'=>1,
'sugarcrm/themes/default/images/tabDownArrowOver.png'=>1,
'sugarcrm/themes/default/images/tabMoreArrow.png'=>1,
'sugarcrm/themes/default/images/tabRowBg.png'=>1,
'sugarcrm/themes/default/images/tabSubDivider.png'=>1,
);
$build = 'com';
