KJE.definitions.set("**INFLATION_DEFINITION**","Lo que espera para el &iacute;ndice promedio de inflaci&oacute;n a largo plazo. Una medida com&uacute;n de inflaci&oacute;n en los Estados Unidos es el &Iacute;ndice de Precios al Consumidor  (Consumer Price Index, CPI), que tiene un promedio a largo plazo del 2.9% anual, de 1925 a 2015.");KJE.definitions.set("**ROR_DEFINITION**",'El por ciento de rendimiento real depender&aacute; en gran parte del tipo de inversi&oacute;n que usted escoja.  De enero de 1970 a diciembre de 2015 el por ciento de rendimiento promedio ponderado para el &iacute;ndice "S&P 500", incluyendo la reinversi&oacute;n de dividendos, fue aproximadamente 10.5% por a&ntilde;o.  Durante estos a&ntilde;os el rendimiento mayor para un periodo de 12 meses fue de 61% mientras el menor fue de -43%.    Las cuentas de ahorro en los bancos pagan un inter&eacute;s tan bajo como 1% o menos. <P>Es importante recordar que el por ciento de rendimiento futuro no se pueden predecir con certeza y que las inversiones que pagan un por ciento de rendimiento mayor est&aacute;n sujetos a un mayor riesgo y volatilidad.  El por ciento de rendimiento real puede variar extensamente durante el su vida, especialmente en inversiones a largo plazo, incluyendo una potencial perdida de principal de su inversi&oacute;n.');KJE.parameters.set("MSG_AGE_CURRENT","Su edad");KJE.parameters.set("MSG_AGE_DESIRED","Edad a la que quiere ser millonario/a");KJE.parameters.set("MSG_AMT_CURRENT","Capital invertido actual");KJE.parameters.set("MSG_AMT_SAVE_MONTH","Ahorros mensuales");KJE.parameters.set("MSG_CURRENT_AGE","Su edad");KJE.parameters.set("MSG_ERROR1","Su edad actual tiene que ser menor que la edad a la que quiere llegar a ser millonario.");KJE.parameters.set("MSG_EXCEED","&iexcl;Usted ser&aacute; millonario!");KJE.parameters.set("MSG_FAIL","Necesita hacer algunos cambios.");KJE.parameters.set("MSG_GRAPH1","Saldo");KJE.parameters.set("MSG_GRAPH2","Saldo despu&eacute;s de inflaci&oacute;n");KJE.parameters.set("MSG_INFLATION_RATE","Tasa de inflaci&oacute;n");KJE.parameters.set("MSG_MADE","Perfecto");KJE.parameters.set("MSG_ROR_INVEST","Tasa de rendimiento");KJE.parameters.set("TITLE_TEMPLATE","&iexcl;El plan actual le podr&iacute;a hacer millonario a los KJE1 a&ntilde;os de edad!");KJE.parameters.set("MSG_DROPPER_TITLE","Metas de ahorro:");KJE.parameters.set("MSG_DROPPER_CLOSETITLE"," ");KJE.parameters.set("MSG_GRAPH_TITLE","A los KJE1 a&ntilde;os el total es de KJE2.");KJE.MillionaireCalc=function(){this.AGE_CURRENT=0;this.AMT_CURRENT=0;this.AMT_SAVE_MONTH=0;this.INFLATION_RATE=0;this.AMT_TARGET=1000000;this.NBR_PERIODS=12;this.PAYMENTS_AT_START=KJE.parameters.get("PAYMENTS_AT_START",true);this.MAX_AGE=100;this.MAX_CURRENT_AMT=1000000;this.MAX_SAVE_MONTH=100000;this.MAX_ROR_INVEST=100/this.NBR_PERIODS;this.MAX_MONTHS_BF_TARGET=this.MAX_AGE*this.NBR_PERIODS;this.YRS_BF_TARGET=0;this.MONTHS_BF_TARGET=0;this.ROR_MONTHLY_PERC=0;this.MSG_EXCEED=KJE.parameters.get("MSG_EXCEED","You're going to be a millionaire!");this.MSG_MADE=KJE.parameters.get("MSG_MADE","Perfect");this.MSG_FAIL=KJE.parameters.get("MSG_FAIL","You need a few savings changes.");this.MSG_ERROR1=KJE.parameters.get("MSG_ERROR1","Your current age must be less than your target age.")};KJE.MillionaireCalc.prototype.clear=function(){this.AGE_DESIRED=0;this.ROR_INVEST=0};KJE.MillionaireCalc.prototype.calculate=function(k){var h=KJE;var j=this.AGE_DESIRED;var e=this.ROR_INVEST;var f="";var i=false;if(this.AGE_CURRENT>=j){throw (this.MSG_ERROR1)}this.YRS_BF_TARGET=j-this.AGE_CURRENT;this.MONTHS_BF_TARGET=this.YRS_BF_TARGET*this.NBR_PERIODS;this.ROR_MONTHLY_PERC=KJE.ROR_MONTH(e/100);this.AMT_AT_TARGET=this.solveAmtAtYear(this.YRS_BF_TARGET);var a=this.solveForMonths(this.AMT_TARGET,this.AMT_CURRENT,this.ROR_MONTHLY_PERC,this.MONTHS_BF_TARGET,this.AMT_SAVE_MONTH);var d=this.solveForCurrentAmt(this.AMT_TARGET,this.AMT_CURRENT,this.ROR_MONTHLY_PERC,this.MONTHS_BF_TARGET,this.AMT_SAVE_MONTH);var m=this.solveForSaveAmt(this.AMT_TARGET,this.AMT_CURRENT,this.ROR_MONTHLY_PERC,this.MONTHS_BF_TARGET,this.AMT_SAVE_MONTH);var g=KJE.FV_AMT(this.solveForROR(this.AMT_TARGET,this.AMT_CURRENT,this.ROR_MONTHLY_PERC,this.MONTHS_BF_TARGET,this.AMT_SAVE_MONTH),this.NBR_PERIODS,1)-1;var l=(a/this.NBR_PERIODS);var b=Math.round(l+this.AGE_CURRENT);var c=KJE.NPV_AMT(this.INFLATION_RATE/100,l,this.AMT_TARGET);if(b<j){f=this.MSG_EXCEED;i=true}else{if(b==j){f=this.MSG_MADE;i=true}else{f=this.MSG_FAIL;i=false}}this.MONTHS_TO_MILLION=a;this.RQD_CURRENT=d;this.RQD_SAVE_MONTH=m;this.RQR_ROR_BFTAX=g;this.YEARS_TO_MILLION=l;this.AGE_AT_MILLION=b;this.NPV_MILLION=c;this.YOU_MADE_IT=f;this.MADE_IT=i};KJE.MillionaireCalc.prototype.formatReport=function(b){var c=KJE;var a=this.iDecimal;var d=b;d=KJE.replace("AGE_CURRENT",c.number(this.AGE_CURRENT),d);d=KJE.replace("AGE_DESIRED",c.number(this.AGE_DESIRED),d);d=KJE.replace("AMT_CURRENT",c.dollars(this.AMT_CURRENT),d);d=KJE.replace("AMT_SAVE_MONTH",c.dollars(this.AMT_SAVE_MONTH),d);d=KJE.replace("ROR_INVEST",c.percent(this.ROR_INVEST/100,2),d);d=KJE.replace("INFLATION_RATE",c.percent(this.INFLATION_RATE/100,1),d);d=KJE.replace("YRS_BF_TARGET",c.number(this.YRS_BF_TARGET),d);d=KJE.replace("MONTHS_BF_TARGET",c.number(this.MONTHS_BF_TARGET),d);d=KJE.replace("ROR_MONTHLY_PERC",c.percent(this.ROR_MONTHLY_PERC),d);d=KJE.replace("MONTHS_TO_MILLION",c.number(this.MONTHS_TO_MILLION),d);d=KJE.replace("RQD_CURRENT",c.dollars(this.RQD_CURRENT),d);d=KJE.replace("RQD_SAVE_MONTH",c.dollars(this.RQD_SAVE_MONTH),d);d=KJE.replace("RQR_ROR_BFTAX",c.percent(this.RQR_ROR_BFTAX,2),d);d=KJE.replace("YEARS_TO_MILLION",c.number(this.YEARS_TO_MILLION),d);d=KJE.replace("AGE_AT_MILLION",c.number(this.AGE_AT_MILLION),d);d=KJE.replace("NPV_MILLION",c.dollars(this.NPV_MILLION),d);d=KJE.replace("YOU_MADE_IT",this.YOU_MADE_IT,d);d=KJE.replace("AMT_TARGET",c.dollars(this.AMT_TARGET),d);d=KJE.replace("NBR_PERIODS",c.number(this.NBR_PERIODS),d);return d};KJE.MillionaireCalc.prototype.solveForMonths=function(a,b,g,d,e){d=this.MAX_MONTHS_BF_TARGET/2;var f=this.MAX_MONTHS_BF_TARGET/4;for(var c=0;c<30;c++){if(this.ifTargetGreater(a,b,g,d,e)){d+=f}else{d-=f}f=f/2}return d};KJE.MillionaireCalc.prototype.getAmts=function(){var b=KJE.FloatArray(this.YRS_BF_TARGET+1);for(var a=0;a<=this.YRS_BF_TARGET;a++){b[a]=(this.solveAmtAtYear(a))}return b};KJE.MillionaireCalc.prototype.getAmtsAfterInflation=function(){var b=KJE.FloatArray(this.YRS_BF_TARGET+1);var c=this.INFLATION_RATE/100;for(var a=0;a<=this.YRS_BF_TARGET;a++){b[a]=KJE.NPV_AMT(c,a,this.solveAmtAtYear(a))}return b};KJE.MillionaireCalc.prototype.getCategories=function(){var b=new Array(this.YRS_BF_TARGET+1);for(var a=0;a<=this.YRS_BF_TARGET;a++){b[a]=(a+this.AGE_CURRENT)+""}return b};KJE.MillionaireCalc.prototype.solveAmtAtYear=function(a){return KJE.FV_AMT(this.ROR_MONTHLY_PERC,a*this.NBR_PERIODS,this.AMT_CURRENT)+(this.PAYMENTS_AT_START?KJE.FV_BEGIN(this.ROR_MONTHLY_PERC,a*this.NBR_PERIODS,this.AMT_SAVE_MONTH):KJE.FV(this.ROR_MONTHLY_PERC,a*this.NBR_PERIODS,this.AMT_SAVE_MONTH))};KJE.MillionaireCalc.prototype.solveForCurrentAmt=function(a,b,g,d,e){b=this.MAX_CURRENT_AMT/2;var f=this.MAX_CURRENT_AMT/4;for(var c=0;c<30;c++){if(this.ifTargetGreater(a,b,g,d,e)){b+=f}else{b-=f}f=f/2}return b};KJE.MillionaireCalc.prototype.solveForSaveAmt=function(a,b,g,d,e){e=this.MAX_SAVE_MONTH/2;var f=this.MAX_SAVE_MONTH/4;for(var c=0;c<30;c++){if(this.ifTargetGreater(a,b,g,d,e)){e+=f}else{e-=f}f=f/2}return e};KJE.MillionaireCalc.prototype.solveForROR=function(a,b,g,d,e){g=this.MAX_ROR_INVEST/2;var f=this.MAX_ROR_INVEST/4;for(var c=0;c<30;c++){if(this.ifTargetGreater(a,b,g,d,e)){g+=f}else{g-=f}f=f/2}return g};KJE.MillionaireCalc.prototype.ifTargetGreater=function(a,b,e,c,d){return(a>KJE.FV_AMT(e,c,b)+(this.PAYMENTS_AT_START?KJE.FV_BEGIN(e,c,d):KJE.FV(e,c,d)))};KJE.CalcName="Calculadora para llegar al mill&oacute;n";KJE.CalcType="SPMillionaire";KJE.CalculatorTitleTemplate="Current plan could make you a millionaire at age KJE1!";KJE.ReportGraphCount=1;KJE.initialize=function(){KJE.CalcControl=new KJE.MillionaireCalc();KJE.GuiControl=new KJE.Millionaire(KJE.CalcControl)};KJE.Millionaire=function(h){var g=KJE;var e=KJE.gLegend;var c=KJE.inputs.items;this.MSG_GRAPH1=KJE.parameters.get("MSG_GRAPH1","Balance");this.MSG_GRAPH2=KJE.parameters.get("MSG_GRAPH2","Balance after inflation");var f=KJE.parameters.get("MSG_DROPPER_TITLE","Millionaire savings plan: ");var d=KJE.parameters.get("MSG_DROPPER_CLOSETITLE","KJE1 plus KJE2 per month earning KJE3 per year.");KJE.NumberSlider("AGE_CURRENT","Your age",0,100,0);KJE.NumberSlider("AGE_DESIRED","Millionaire target age",1,100,0);KJE.Slider("AMT_CURRENT","Amount currently invested",0,10000000,0,g.FMT_DOLLARS,1,KJE.s_label[4],KJE.useScale(4));KJE.Slider("AMT_SAVE_MONTH","Savings per month",0,10000,0,g.FMT_DOLLARS,1,KJE.s_label[0],KJE.useScale(0));KJE.InvestRateSlider("ROR_INVEST","Expected Rate of return");KJE.InflationRateSlider("INFLATION_RATE","Expected inflation rate");var a=KJE.gNewGraph(KJE.gCOLUMN,"GRAPH1",true,false,KJE.colorList[1],KJE.parameters.get("MSG_GRAPH_TITLE","Total at age KJE1 is KJE2"));a._legend._iOrientation=(e.GRID_TOP_LEFT);a._titleXAxis.setText(c.AGE_CURRENT.getName());a._titleYAxis.setText(KJE.sCurrency);var b=function(){return f+KJE.subText(KJE.getKJEReplaced(d,c.AMT_CURRENT.getFormatted(),c.AMT_SAVE_MONTH.getFormatted(),c.ROR_INVEST.getFormatted()),"KJECenter")};KJE.addDropper(new KJE.Dropper("INPUTS",true,f,b),KJE.colorList[0])};KJE.Millionaire.prototype.setValues=function(b){var a=KJE.inputs.items;b.AGE_CURRENT=a.AGE_CURRENT.getValue();b.AGE_DESIRED=a.AGE_DESIRED.getValue();b.AMT_CURRENT=a.AMT_CURRENT.getValue();b.AMT_SAVE_MONTH=a.AMT_SAVE_MONTH.getValue();b.ROR_INVEST=a.ROR_INVEST.getValue();b.INFLATION_RATE=a.INFLATION_RATE.getValue()};KJE.Millionaire.prototype.refresh=function(e){var d=KJE;var c=KJE.gLegend;var b=KJE.inputs.items;var a=KJE.gGraphs[0];KJE.setTitleTemplate(Math.round(e.AGE_AT_MILLION));a.removeAll();a.setGraphCategories(e.getCategories());a.add(new KJE.gGraphDataSeries(e.getAmts(),this.MSG_GRAPH1,a.getColor(1)));a.add(new KJE.gGraphDataSeries(e.getAmtsAfterInflation(),this.MSG_GRAPH2,a.getColor(2)));a.setTitleTemplate(e.AGE_DESIRED,d.dollars(e.AMT_AT_TARGET));a.paint()};KJE.InputScreenText=" <div id=KJE-D-INPUTS><div id=KJE-P-INPUTS>Input information:</div></div> <div id=KJE-E-INPUTS > <div id='KJE-C-AGE_CURRENT'><input id='KJE-AGE_CURRENT' /></div> <div id='KJE-C-AGE_DESIRED'><input id='KJE-AGE_DESIRED' /></div> <div id='KJE-C-AMT_CURRENT'><input id='KJE-AMT_CURRENT' /></div> <div id='KJE-C-AMT_SAVE_MONTH'><input id='KJE-AMT_SAVE_MONTH' /></div> <div id='KJE-C-ROR_INVEST'><input id='KJE-ROR_INVEST' /></div> <div id='KJE-C-INFLATION_RATE'><input id='KJE-INFLATION_RATE' /></div> <div style=\"height:10px\"></div> </div> **GRAPH1** ";KJE.DefinitionText=" <div id='KJE-D-AGE_DESIRED' ><dt>Edad a la que quiere ser millonario</dt><dd>Edad a la que quiere ser millonario.</dd></div> <div id='KJE-D-AMT_CURRENT' ><dt>Capital actual invertido</dt><dd>Valor de todas sus inversiones actuales.</dd></div> <div id='KJE-D-AMT_SAVE_MONTH' ><dt>Ahorros mensuales</dt><dd>Monto que aportar&aacute; cada mes a sus inversiones.</dd></div> <div id='KJE-D-ROR_INVEST' ><dt>Tasa de rendimiento proyectada</dt><dd>Tasa de rendimiento combinada anual que usted espera que sus inversiones le proporcionen. **ROR_DEFINITION**</dd></div> <div id='KJE-D-INFLATION_RATE' ><dt>Tasa de inflaci&oacute;n proyectada</dt><dd>Es la tasa de inflaci&oacute;n promedio a largo plazo. Este es un valor estimado basado en la econom&iacute;a actual. La tasa actual podr&iacute;a variar significativamente.</dd></div> ";KJE.ReportText=' <h2 class=\'KJEReportHeader KJEFontHeading\'>Con su plan de ahorros actual, usted ser&aacute; millonario a los AGE_AT_MILLION a&ntilde;os.</h2> Para llegar a ser millonario a los AGE_DESIRED a&ntilde;os tiene que hacer una de las siguientes cosas: <ul> <li>Llevar sus ahorros mensuales a <strong>RQD_SAVE_MONTH</strong>.</li> <li>Llevar el monto invertido actual a <strong>RQD_CURRENT</strong>.</li> <li>Contar con una tasa de rendimiento del <strong>RQR_ROR_BFTAX</strong>.</li> </ul> <p>Cuando usted tenga AGE_AT_MILLION a&ntilde;os, un mill&oacute;n de d&oacute;lares equivaldr&aacute; a NPV_MILLION, ajustados por una tasa de inflaci&oacute;n del INFLATION_RATE. <p>**GRAPH**<p> <div class=KJEReportTableDiv><table class=KJEReportTable><caption class=\'KJEHeaderRow KJEHeading\'>Valores Ingresados</caption> <tr class=KJEOddRow><td class="KJELabel KJECellBorder KJECell60">Su edad:</td><td class="KJECell KJECell40">AGE_CURRENT</td></tr> <tr class=KJEEvenRow><td class="KJELabel KJECellBorder">Edad a la que quiere ser millonario/a:</td><td class="KJECell">AGE_DESIRED</td></tr> <tr class=KJEOddRow><td class="KJELabel KJECellBorder">Capital invertido actual:</td><td class="KJECell">AMT_CURRENT</td></tr> <tr class=KJEEvenRow><td class="KJELabel KJECellBorder">Monto mensual que puede ahorrar:</td><td class="KJECell">AMT_SAVE_MONTH</td></tr> <tr class=KJEOddRow><td class="KJELabel KJECellBorder">Tasa de rendimiento proyectada:</td><td class="KJECell">ROR_INVEST</td></tr> <tr class=KJEEvenRow><td class="KJELabel KJECellBorder">Tasa de inflaci&oacute;n:</td><td class="KJECell">INFLATION_RATE</td></tr></table> </div> ';