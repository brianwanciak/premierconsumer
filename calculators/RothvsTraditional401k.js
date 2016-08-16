KJE.Default.TaxRate=25;KJE.Default.TaxRateRetire=15;KJE.Default.TaxRateCapGain=15;KJE.Default.StateTaxRate=8;KJE.Default.IncomeTaxTableTaxYear="Use the table below to assist you in estimating your federal tax rate. <div class='KJEReportTableDiv'><table class='KJEReportTable KJEReportTableShrink'><caption class='KJEHeaderRow KJEHeading'>Filing Status and Income Tax Rates 2016<sup>*</sup></caption><thead><tr class=KJEFooterRow><th class='KJELabel KJECell10 KJECenter' style='vertical-align:bottom;'>Tax Rate</th><th class='KJELabel KJECell225 KJECenter' style='vertical-align:bottom;'>Married Filing Jointly or Qualified Widow(er)</th><th class='KJELabel KJECell225 KJECenter' style='vertical-align:bottom;'>Single</th><th class='KJELabel KJECell225 KJECenter' style='vertical-align:bottom;'>Head of Household</th><th class='KJELabel KJECell225 KJECenter' style='vertical-align:bottom;'>Married Filing Separately</th></tr></thead><tr class='KJEOddRow'><td class='KJELabel KJECellBorder KJELabelPad'>10%</td><td class='KJECell KJECellBorder'>$0 -&nbsp;$18,550</td><td class='KJECell KJECellBorder'>$0 -&nbsp;$9,275</td><td class='KJECell KJECellBorder'>$0 -&nbsp;$13,250</td><td class='KJECell'>$0 -&nbsp;$9,275</td></tr><tr class=KJEEvenRow style='margin-top:10px;'><td class='KJELabel KJECellBorder KJELabelPad'>15%</td><td class='KJECell KJECellBorder'>$18,550 -&nbsp;$75,300</td><td class='KJECell KJECellBorder'>$9,275 -&nbsp;$37,650</td><td class='KJECell KJECellBorder'>$13,250 -&nbsp;$50,400</td><td class='KJECell'>$9,275 -&nbsp;$37,650</td></tr><tr class=KJEOddRow><td class='KJELabel KJECellBorder KJELabelPad'>25%</td><td class='KJECell KJECellBorder'>$75,300 -&nbsp;$151,900</td><td class='KJECell KJECellBorder'>$37,650 -&nbsp;$91,150</td><td class='KJECell KJECellBorder'>$50,400 -&nbsp;$130,150</td><td class='KJECell'>$37,650 -&nbsp;$75,950</td></tr><tr class=KJEEvenRow><td class='KJELabel KJECellBorder KJELabelPad'>28%</td><td class='KJECell KJECellBorder'>$151,900 -&nbsp;$231,450</td><td class='KJECell KJECellBorder'>$91,150 -&nbsp;$190,150</td><td class='KJECell KJECellBorder'>$130,150 -&nbsp;$210,800</td><td class='KJECell'>$75,950 -&nbsp;$115,725</td></tr><tr class=KJEOddRow><td class='KJELabel KJECellBorder KJELabelPad'>33%</td><td class='KJECell KJECellBorder'>$231,450  -$413,350</td><td class='KJECell KJECellBorder'>$190,150 -&nbsp;$413,350</td><td class='KJECell KJECellBorder'>$210,800 -&nbsp;$413,350</td><td class='KJECell'>$115,725 -&nbsp;$206,675</td></tr><tr class=KJEEvenRow><td class='KJELabel KJECellBorder KJELabelPad'>35%</td><td class='KJECell KJECellBorder'>$413,350  -$466,950</td><td class='KJECell KJECellBorder'>$413,350 -&nbsp;$415,050</td><td class='KJECell KJECellBorder'>$413,350 -&nbsp;$441,000</td><td class='KJECell'>$206,675 -&nbsp;$233,475</td></tr><tr class='KJEOddRow'><td class='KJELabel KJECellBorder KJELabelPad'>39.6%</td><td class='KJECell KJECellBorder'>Over$466,950 </td><td class='KJECell KJECellBorder'>Over$415,050 </td><td class='KJECell KJECellBorder'>Over$441,000 </td><td class='KJECell'>Over$233,475 </td></tr></table><div align=center><br><sup>*</sup>Caution: Do not use these tax rate schedules to figure 2015 taxes. Use only to figure 2016 estimates. Source: 2015 Rev. Proc. 2015-61</div></div>";KJE.Default.IncomeTaxTableCurrent=KJE.Default.IncomeTaxTableTaxYear;KJE.definitions.set("**TAXTABLE_CURRENT_DEFINITION**",KJE.Default.IncomeTaxTableCurrent);KJE.definitions.set("**TAXTABLE_TAXYEAR_DEFINITION**",KJE.Default.IncomeTaxTableTaxYear);KJE.RothvsTraditional401kCalc=function(){this.SAVE_TAX=KJE.parameters.get("SAVE_TAXES",true);this.ALLOWABLE_MAX=0;this.iDECIMAL=0;this.ANNUAL_PERIODS=1;this.ROR_CALC_WRONG=false;this.CONTRIBUTE_MAXIMUM=0;this.bCONTRIBUTE_MAX=false;this.b403=null;this.dTaxRegardless=0;this.TRADITIONAL_WITHDRAWAL_TAX=0;this.TRADITIONAL_DEPOSIT_TAX_SAVINGS=0;this.bLump=false;this.ANNUAL_MAX=[18000,18000,18000,18000];this.CATCHUP_MAX=[6000,6000,6000,6000];this.DS_TOTAL_TAXABLE=KJE.FloatArray(1);this.DS_TOTAL2=KJE.FloatArray(2);this.DS_ANNUAL_WITHDRAW_BT=KJE.FloatArray(2);this.DS_ANNUAL_WITHDRAW_AF=KJE.FloatArray(2);this.DS_ANNUAL_WITHDRAW_TAXABLE_ACCOUNT=KJE.FloatArray(2);this.DS_BEFORE_TAXES=KJE.FloatArray(2);this.DS_TOTAL=KJE.FloatArray(2);this.MSG_ERROR2=KJE.parameters.get("MSG_ERROR2","Your current age must be less than your age at retirement.");this.MSG_ERROR1=KJE.parameters.get("MSG_ERROR1","Your annual contribution for 2016 cannot exceed");this.MSG_CONTRIBUTE_LBL=KJE.parameters.get("MSG_CONTRIBUTE_LBL","<BR>(Contributions increase to the maximum allowed each year)");this.MSG_GOROTH_SHORT=KJE.parameters.get("MSG_GOROTH_SHORT","A Roth 401(k) may be worth KJE1 more than a traditional 401(k).");this.MSG_GOTRAD_SHORT=KJE.parameters.get("MSG_GOTRAD_SHORT","A traditional 401(k) may be worth KJE1 more than a Roth 401(k).");this.MSG_GOROTH=KJE.parameters.get("MSG_GOROTH","Based on the information you entered, a Roth account may be worth more than a traditional account.");this.MSG_GOTRAD=KJE.parameters.get("MSG_GOTRAD","Based on the information you entered, a traditional account may be worth more than a Roth account.");this.MSG_GOSAME=KJE.parameters.get("MSG_GOSAME","Based on the information you entered, a traditional account may be the worth the same as a Roth account.");this.MSG_DISTRIBUTION_TYPE1=["a lump-sum withdrawal","annual withdrawals"];this.MSG_DISTRIBUTION_TYPE1[0]=KJE.parameters.get("MSG_DISTRIBUTION_TYPE11",this.MSG_DISTRIBUTION_TYPE1[0]);this.MSG_DISTRIBUTION_TYPE1[1]=KJE.parameters.get("MSG_DISTRIBUTION_TYPE12",this.MSG_DISTRIBUTION_TYPE1[1]);this.MSG_DISTRIBUTION_TYPE2=["the lump-sum withdrawal","annual withdrawals"];this.MSG_DISTRIBUTION_TYPE2[0]=KJE.parameters.get("MSG_DISTRIBUTION_TYPE21",this.MSG_DISTRIBUTION_TYPE2[0]);this.MSG_DISTRIBUTION_TYPE2[1]=KJE.parameters.get("MSG_DISTRIBUTION_TYPE22",this.MSG_DISTRIBUTION_TYPE2[1]);this.MSG_DISTRIBUTION_TYPE3=["","each year"];this.MSG_DISTRIBUTION_TYPE3[0]=KJE.parameters.get("MSG_DISTRIBUTION_TYPE31",this.MSG_DISTRIBUTION_TYPE3[0]);this.MSG_DISTRIBUTION_TYPE3[1]=KJE.parameters.get("MSG_DISTRIBUTION_TYPE32",this.MSG_DISTRIBUTION_TYPE3[1]);this.MSG_DISTRIBUTION_TYPE4=["additional income","additional annual income"];this.MSG_DISTRIBUTION_TYPE4[0]=KJE.parameters.get("MSG_DISTRIBUTION_TYPE41",this.MSG_DISTRIBUTION_TYPE4[0]);this.MSG_DISTRIBUTION_TYPE4[1]=KJE.parameters.get("MSG_DISTRIBUTION_TYPE42",this.MSG_DISTRIBUTION_TYPE4[1]);this.MSG_DISTRIBUTION_TYPE5=["withdrawal","annual withdrawals"];this.MSG_DISTRIBUTION_TYPE5[0]=KJE.parameters.get("MSG_DISTRIBUTION_TYPE51",this.MSG_DISTRIBUTION_TYPE5[0]);this.MSG_DISTRIBUTION_TYPE5[1]=KJE.parameters.get("MSG_DISTRIBUTION_TYPE52",this.MSG_DISTRIBUTION_TYPE5[1]);this.MSG_DISCLAIMER_ONE=["","The analyzer calculates average annual withdrawal amounts by assuming that no balance remains when withdrawals end."];this.MSG_DISCLAIMER_ONE[0]=KJE.parameters.get("MSG_DISCLAIMER_ONE1",this.MSG_DISTRIBUTION_TYPE5[0]);this.MSG_DISCLAIMER_ONE[1]=KJE.parameters.get("MSG_DISCLAIMER_ONE2",this.MSG_DISTRIBUTION_TYPE5[1]);this.MSG_DISCLAIMER_TWO=["Taxes on the traditional account withdrawal would be this.TRADITIONAL_WITHDRAWAL_TAX.","Annual taxes on traditional account withdrawals would be this.TRADITIONAL_WITHDRAWAL_TAX."];this.MSG_DISCLAIMER_TWO[0]=KJE.parameters.get("MSG_DISCLAIMER_TWO1",this.MSG_DISCLAIMER_TWO[0]);this.MSG_DISCLAIMER_TWO[1]=KJE.parameters.get("MSG_DISCLAIMER_TWO2",this.MSG_DISCLAIMER_TWO[1]);this.MSG_DISCLAIMER_THREE=["If invested in a taxable account with the same rates of return as the retirement plan, the tax savings could be worth TOTAL_TAXABLE at retirement and could provide TRADITIONAL_ANNUAL_WITHDRAW_TAXABLE_ACCOUNT in addition to the traditional account withdrawal.","If invested in a taxable account with the same rates of return as the retirement plan	the tax savings could be worth TOTAL_TAXABLE at retirement and could provide TRADITIONAL_ANNUAL_WITHDRAW_TAXABLE_ACCOUNT a year in addition to the traditional account withdrawals."];this.MSG_DISCLAIMER_THREE[0]=KJE.parameters.get("MSG_DISCLAIMER_THREE1",this.MSG_DISCLAIMER_THREE[0]);this.MSG_DISCLAIMER_THREE[1]=KJE.parameters.get("MSG_DISCLAIMER_THREE2",this.MSG_DISCLAIMER_THREE[1]);this.total_cats=["Traditional 401(k)","Roth 401(k)"];this.total_cats[0]=KJE.parameters.get("MSG_GRAPH_CAT1",this.total_cats[0]);this.total_cats[1]=KJE.parameters.get("MSG_GRAPH_CAT2",this.total_cats[1]);this.MSG_SHORT_RESULT=["You should consider a Roth account.","You should consider a traditional account.","A traditional account would be the same as a Roth account"];this.MSG_SHORT_RESULT[0]=KJE.parameters.get("MSG_SHORT_RESULT1",this.MSG_SHORT_RESULT[0]);this.MSG_SHORT_RESULT[1]=KJE.parameters.get("MSG_SHORT_RESULT2",this.MSG_SHORT_RESULT[1]);this.MSG_SHORT_RESULT[2]=KJE.parameters.get("MSG_SHORT_RESULT3",this.MSG_SHORT_RESULT[2]);this.MSG_LONG_RESULT=["The additional income would not make up for the taxes paid on withdrawals. <strong>Therefore, a Roth account might be better than a traditional account in this situation.</strong>","The additional income would be more than the taxes paid on withdrawals. <strong>Therefore, a traditional account might be better than a Roth account in this situation.</strong>","The additional income would be equal to the taxes paid on withdrawals. <strong>Therefore, a traditional account might be the same as a Roth account in this situation.</strong>"];this.MSG_LONG_RESULT[0]=KJE.parameters.get("MSG_LONG_RESULT1",this.MSG_LONG_RESULT[0]);this.MSG_LONG_RESULT[1]=KJE.parameters.get("MSG_LONG_RESULT2",this.MSG_LONG_RESULT[1]);this.MSG_LONG_RESULT[2]=KJE.parameters.get("MSG_LONG_RESULT3",this.MSG_LONG_RESULT[2]);this.MSG_PLACE_HOLDER=KJE.parameters.get("MSG_PLACE_HOLDER","-");this.sSchedule=new KJE.Repeating()};KJE.RothvsTraditional401kCalc.prototype.clear=function(){this.CURRENT_AGE=0;this.ANNUAL_CONTRIBUTION=0;this.RATE_OF_RETURN=0;this.AGE_OF_RETIREMENT=0;this.CURRENT_TAX_RATE=0;this.RETIREMENT_TAX_RATE=0;this.YEARS_OF_WITHDRAWALS=1;this.RETIREMENT_RATE_OF_RETURN=-1;this.YEARS_UNTIL_RETIREMENT=0};KJE.RothvsTraditional401kCalc.prototype.calculate=function(F){var k=KJE;var r=this.CURRENT_AGE;var J=this.ANNUAL_CONTRIBUTION;var g=this.RATE_OF_RETURN;var G=this.AGE_OF_RETIREMENT;var c=this.CURRENT_TAX_RATE;var q=this.RETIREMENT_TAX_RATE;var K=this.YEARS_OF_WITHDRAWALS;var h=this.RETIREMENT_RATE_OF_RETURN;var w=0;var y=0;var a="";if(G!=0&&r!=0){if(G<=r){throw (this.MSG_ERROR2)}this.YEARS_UNTIL_RETIREMENT=G-r}if(h==-1){h=g}this.CONTRIBUTE_MAXIMUM=this.dMaximumContribution(0,r);if(this.bCONTRIBUTE_MAX){J=this.CONTRIBUTE_MAXIMUM}if(J>this.CONTRIBUTE_MAXIMUM){throw (this.MSG_ERROR1+" "+k.dollars(this.CONTRIBUTE_MAXIMUM,this.iDECIMAL)+".")}var t=Math.round(this.YEARS_UNTIL_RETIREMENT);var A=0;this.DD_V401K_AF_TAX=KJE.FloatArray(t);this.DD_ROTH=KJE.FloatArray(t);this.DD_V401K_BF_TAX=KJE.FloatArray(t);this.DD_TAXABLE=KJE.FloatArray(t);this.DD_CONTRIBUTION=KJE.FloatArray(t);this.DD_TAX_CONTRIBUTION=KJE.FloatArray(t);var s=0;var f=0;var D=0;var v=g/100;var E=c/100;this.TRADITIONAL_DEPOSIT_TAX_SAVINGS=(this.SAVE_TAX?k.round(J*E,this.iDECIMAL):0);var I=this.CONTRIBUTE_MAXIMUM;var B=q/100;var H=(this.ROR_CALC_WRONG?(v/this.ANNUAL_PERIODS):KJE.ROR_PERIOD(v,this.ANNUAL_PERIODS));var o=(this.ROR_CALC_WRONG?((v/this.ANNUAL_PERIODS)*(1-E)):KJE.ROR_PERIOD(v*(1-E),this.ANNUAL_PERIODS));var d=KJE.FV_AMT(v/12,12,1)-1;var z=KJE.FV_AMT((v*(1-E))/12,12,1)-1;this.dTaxRegardless=0;var u=0;for(var x=0;x<this.YEARS_UNTIL_RETIREMENT;x++){I=this.dMaximumContribution(x,r);this.DD_CONTRIBUTION[x]=this.dActualContribution(x,I,J,this.bCONTRIBUTE_MAX);var e=this.dActualDeduction(this.DD_CONTRIBUTION[x],I);u=k.round((e*E),this.iDECIMAL);this.DD_TAX_CONTRIBUTION[x]=(this.SAVE_TAX?u:0);s+=this.DD_CONTRIBUTION[x];f=k.round((f*(1+(this.ROR_CALC_WRONG?d:v)))+KJE.FV_BEGIN(H,this.ANNUAL_PERIODS,this.DD_CONTRIBUTION[x]/this.ANNUAL_PERIODS),2);D=k.round((D*(1+(this.ROR_CALC_WRONG?z:(v*(1-E)))))+KJE.FV_BEGIN(o,this.ANNUAL_PERIODS,this.DD_TAX_CONTRIBUTION[x]/this.ANNUAL_PERIODS),2);this.dTaxRegardless=k.round((this.dTaxRegardless*(1+(this.ROR_CALC_WRONG?z:(v*(1-E)))))+KJE.FV_BEGIN(o,this.ANNUAL_PERIODS,u/this.ANNUAL_PERIODS),2);this.DD_V401K_BF_TAX[x]=f;this.DD_TAXABLE[x]=D;this.DD_ROTH[x]=f;this.DD_V401K_AF_TAX[x]=(f)*(1-B)+D}f=k.round(f,this.iDECIMAL);var m=f;var b=k.round((f)*(1-B),this.iDECIMAL);var C=b+D;var p=m-C;if(p>=0){a=KJE.getKJEReplaced(this.MSG_GOROTH_SHORT,k.dollars(p,this.iDECIMAL))}else{p*=-1;a=KJE.getKJEReplaced(this.MSG_GOTRAD_SHORT,k.dollars(p,this.iDECIMAL))}if(this.b403){a=KJE.replace("401(k)","401(k) or 403(b)",a)}var j=(K<=1?0:K);this.bLump=(K<=1);v=h/100;d=KJE.FV_AMT(v/12,12,1)-1;this.DS_ANNUAL_WITHDRAW_BT[0]=k.round(KJE.PMT_BEGIN((this.ROR_CALC_WRONG?d:v),j,f),this.iDECIMAL);this.TRADITIONAL_WITHDRAWAL_TAX=k.round(this.DS_ANNUAL_WITHDRAW_BT[0]*(q/100),this.iDECIMAL);this.DS_ANNUAL_WITHDRAW_AF[0]=k.round(this.DS_ANNUAL_WITHDRAW_BT[0]-this.TRADITIONAL_WITHDRAWAL_TAX,this.iDECIMAL);this.DS_ANNUAL_WITHDRAW_TAXABLE_ACCOUNT[0]=k.round(KJE.PMT_BEGIN((this.ROR_CALC_WRONG?d:v)*(1-B),j,D),this.iDECIMAL);this.DS_ANNUAL_WITHDRAW_BT[1]=k.round(KJE.PMT_BEGIN((this.ROR_CALC_WRONG?d:v),j,m),this.iDECIMAL);this.DS_ANNUAL_WITHDRAW_AF[1]=this.DS_ANNUAL_WITHDRAW_BT[1];this.DS_ANNUAL_WITHDRAW_TAXABLE_ACCOUNT[1]=0;this.DS_BEFORE_TAXES[0]=(f);this.DS_TOTAL[0]=(b);this.DS_BEFORE_TAXES[1]=(m);this.DS_TOTAL[1]=(m);this.DS_TOTAL2[0]=(m);this.DS_TOTAL2[1]=(C);this.cats=KJE.FloatArray(t);if(F){var l=this.sSchedule;l.clearRepeat();l.addHeader(l.sReportCol("Age",1),l.sReportCol("Traditional 401(k)",2),(this.SAVE_TAX?l.sReportCol("Traditional 401(k) + Invested Tax Savings - Taxes",3):l.sReportCol("Traditional 401(k) - Taxes",5)),l.sReportCol("Roth 401(k)",4))}for(var x=1;x<=t;x++){A=x-1;this.cats[A]=k.number(A+r);if(F){l.addRepeat(this.cats[A],""+k.dollars(this.DD_V401K_BF_TAX[A],this.iDECIMAL),""+k.dollars(this.DD_V401K_AF_TAX[A],this.iDECIMAL),""+k.dollars(this.DD_ROTH[A],this.iDECIMAL))}}this.DEDUCTIBLE_PERCENT=w;this.V401K_TAX_SAVINGS=y;this.TOTAL_CONTRIBUTIONS=s;this.V401K_TOTAL_BF_TAX=f;this.V401K_TOTAL_AF_TAX=b;this.TOTAL_TAXABLE=D;this.TOTAL_ROTH=m;this.TOTAL_401K=C;this.RESULTS_MSG=a;this.TOTAL_DIFFERENCE=p;this.ANNUAL_CONTRIBUTION=J};KJE.RothvsTraditional401kCalc.prototype.formatReport=function(b){var c=KJE;var a=this.iDECIMAL;var d=b;if(this.SAVE_TAX){d=KJE.replace("<!--SHOW_TAXABLE-->","",d);d=KJE.replace("<!--/SHOW_TAXABLE-->","",d);d=KJE.replace("<!--DONT_SHOW_TAXABLE-->","<!--",d);d=KJE.replace("<!--/DONT_SHOW_TAXABLE-->","-->",d)}else{d=KJE.replace("<!--SHOW_TAXABLE-->","<!--",d);d=KJE.replace("<!--/SHOW_TAXABLE-->","-->",d);d=KJE.replace("<!--DONT_SHOW_TAXABLE-->","",d);d=KJE.replace("<!--/DONT_SHOW_TAXABLE-->","",d)}if(this.DS_ANNUAL_WITHDRAW_AF[1]+this.DS_ANNUAL_WITHDRAW_TAXABLE_ACCOUNT[1]>this.DS_ANNUAL_WITHDRAW_AF[0]+this.DS_ANNUAL_WITHDRAW_TAXABLE_ACCOUNT[0]){d=KJE.replace("MSG_SHORT_RESULT",this.MSG_SHORT_RESULT[0],d);d=KJE.replace("MSG_GO",this.MSG_GOROTH,d);d=KJE.replace("MSG_LONG_RESULT",this.MSG_LONG_RESULT[0],d)}else{if(this.DS_ANNUAL_WITHDRAW_AF[1]+this.DS_ANNUAL_WITHDRAW_TAXABLE_ACCOUNT[1]<this.DS_ANNUAL_WITHDRAW_AF[0]+this.DS_ANNUAL_WITHDRAW_TAXABLE_ACCOUNT[0]){d=KJE.replace("MSG_SHORT_RESULT",this.MSG_SHORT_RESULT[1],d);d=KJE.replace("MSG_GO",this.MSG_GOTRAD,d);d=KJE.replace("MSG_LONG_RESULT",this.MSG_LONG_RESULT[1],d)}else{d=KJE.replace("MSG_SHORT_RESULT",this.MSG_SHORT_RESULT[2],d);d=KJE.replace("MSG_GO",this.MSG_GOSAME,d);d=KJE.replace("MSG_LONG_RESULT",this.MSG_LONG_RESULT[2],d)}}d=KJE.replace("MSG_DISTRIBUTION_TYPE1",this.MSG_DISTRIBUTION_TYPE1[this.bLump?0:1],d);d=KJE.replace("MSG_DISTRIBUTION_TYPE2",this.MSG_DISTRIBUTION_TYPE2[this.bLump?0:1],d);d=KJE.replace("MSG_DISTRIBUTION_TYPE3",this.MSG_DISTRIBUTION_TYPE3[this.bLump?0:1],d);d=KJE.replace("MSG_DISTRIBUTION_TYPE4",this.MSG_DISTRIBUTION_TYPE4[this.bLump?0:1],d);d=KJE.replace("MSG_DISTRIBUTION_TYPE5",this.MSG_DISTRIBUTION_TYPE5[this.bLump?0:1],d);d=KJE.replace("MSG_DISCLAIMER_ONE",this.MSG_DISCLAIMER_ONE[this.bLump?0:1],d);d=KJE.replace("MSG_DISCLAIMER_TWO",this.MSG_DISCLAIMER_TWO[this.bLump?0:1],d);d=KJE.replace("MSG_DISCLAIMER_THREE",this.MSG_DISCLAIMER_THREE[this.bLump?0:1],d);d=KJE.replace("YEARS_OF_WITHDRAWALS",c.number(this.YEARS_OF_WITHDRAWALS,0),d);d=KJE.replace("MSG_CONTRIBUTE_LBL",(this.bCONTRIBUTE_MAX?this.MSG_CONTRIBUTE_LBL:""),d);d=KJE.replace("CURRENT_AGE",c.number(this.CURRENT_AGE,0),d);d=KJE.replace("ANNUAL_CONTRIBUTION",c.dollars(this.ANNUAL_CONTRIBUTION,a),d);d=KJE.replace("RETIREMENT_RATE_OF_RETURN",(this.bLump?this.MSG_PLACE_HOLDER:c.percent((this.RETIREMENT_RATE_OF_RETURN<0?this.RATE_OF_RETURN:this.RETIREMENT_RATE_OF_RETURN)/100,0)),d);d=KJE.replace("RATE_OF_RETURN",c.percent(this.RATE_OF_RETURN/100,0),d);d=KJE.replace("AGE_OF_RETIREMENT",c.number(this.AGE_OF_RETIREMENT,0),d);d=KJE.replace("CURRENT_TAX_RATE",c.percent(this.CURRENT_TAX_RATE/100,1),d);d=KJE.replace("RETIREMENT_TAX_RATE",c.percent(this.RETIREMENT_TAX_RATE/100,1),d);d=KJE.replace("YEARS_UNTIL_RETIREMENT",c.number(this.YEARS_UNTIL_RETIREMENT,0),d);d=KJE.replace("DEDUCTIBLE_PERCENT",c.percent(this.DEDUCTIBLE_PERCENT,0),d);d=KJE.replace("V401K_TAX_SAVINGS",c.dollars(this.V401K_TAX_SAVINGS,a),d);d=KJE.replace("TOTAL_CONTRIBUTIONS",c.dollars(this.TOTAL_CONTRIBUTIONS,a),d);d=KJE.replace("V401K_TOTAL_BF_TAX",c.dollars(this.V401K_TOTAL_BF_TAX,a),d);d=KJE.replace("V401K_TOTAL_TAXES",c.dollars(this.V401K_TOTAL_BF_TAX-this.V401K_TOTAL_AF_TAX,a),d);d=KJE.replace("V401K_TOTAL_AF_TAX",c.dollars(this.V401K_TOTAL_AF_TAX,a),d);d=KJE.replace("TOTAL_TAXABLE",c.dollars(this.TOTAL_TAXABLE,a),d);d=KJE.replace("TOTAL_REGARD_TAXABLE",c.dollars(this.dTaxRegardless,a),d);d=KJE.replace("TOTAL_401K",c.dollars(this.TOTAL_401K,a),d);d=KJE.replace("TOTAL_DIFFERENCE",c.dollars(this.TOTAL_DIFFERENCE,a),d);d=KJE.replace("RESULTS_MSG",this.RESULTS_MSG,d);d=KJE.replace("TOTAL_ROTH",c.dollars(this.TOTAL_ROTH,a),d);d=KJE.replace("TRADITIONAL_WITHDRAWAL_TAX",c.dollars(this.TRADITIONAL_WITHDRAWAL_TAX,a),d);d=KJE.replace("TRADITIONAL_DEPOSIT_TAX_SAVINGS",c.dollars(this.TRADITIONAL_DEPOSIT_TAX_SAVINGS,a),d);d=KJE.replace("ROTH_ANNUAL_WITHDRAW_BT",c.dollars(this.DS_ANNUAL_WITHDRAW_BT[1],a),d);d=KJE.replace("ROTH_ANNUAL_WITHDRAW_AF",c.dollars(this.DS_ANNUAL_WITHDRAW_AF[1],a),d);d=KJE.replace("ROTH_ANNUAL_WITHDRAW_TAXABLE_ACCOUNT",c.dollars(this.DS_ANNUAL_WITHDRAW_TAXABLE_ACCOUNT[1],a),d);d=KJE.replace("TRADITIONAL_ANNUAL_WITHDRAW_BT",c.dollars(this.DS_ANNUAL_WITHDRAW_BT[0],a),d);d=KJE.replace("TRADITIONAL_ANNUAL_WITHDRAW_AF",c.dollars(this.DS_ANNUAL_WITHDRAW_AF[0],a),d);d=KJE.replace("TRADITIONAL_ANNUAL_WITHDRAW_TAXABLE_ACCOUNT",c.dollars(this.DS_ANNUAL_WITHDRAW_TAXABLE_ACCOUNT[0],a),d);d=d.replace("**REPEATING GROUP**",this.sSchedule.getRepeat());this.sSchedule.clearRepeat();return d};KJE.RothvsTraditional401kCalc.prototype.dMaximumContribution=function(c,a){if(this.ALLOWABLE_MAX!=0){return this.ALLOWABLE_MAX}var b=(c>=this.ANNUAL_MAX.length?this.ANNUAL_MAX.length-1:c);return this.ANNUAL_MAX[b]+((a+c)<50?0:this.CATCHUP_MAX[b])};KJE.RothvsTraditional401kCalc.prototype.dActualContribution=function(d,b,c,a){return(a&&d!=0?b:c)};KJE.RothvsTraditional401kCalc.prototype.dActualDeduction=function(b,a){return(b>a?a:b)};KJE.CalcName="Roth 401(k) vs. Traditional 401(k)";KJE.CalcType="RothvsTraditional401k";KJE.CalculatorTitleTemplate="Roth 401(k) vs. Traditional 401(k)?";KJE.initialize=function(){KJE.CalcControl=new KJE.RothvsTraditional401kCalc();KJE.GuiControl=new KJE.RothvsTraditional401k(KJE.CalcControl)};KJE.RothvsTraditional401k=function(h){var b=KJE;var a=KJE.gLegend;var e=KJE.inputs.items;this.MSG_GRAPH4=KJE.parameters.get("MSG_GRAPH4","Roth 401(k)");this.MSG_GRAPH5=KJE.parameters.get("MSG_GRAPH5","Trad. 401(k) + Tax Savings");this.MSG_GRAPH6=KJE.parameters.get("MSG_GRAPH6","Before taxes");this.MSG_GRAPH7=KJE.parameters.get("MSG_GRAPH7","Total after taxes");this.MSG_GRAPH8=KJE.parameters.get("MSG_GRAPH8","Trad. 401(k)");KJE.NumberSlider("CURRENT_AGE","Current age",1,70,0);KJE.DollarSlider("ANNUAL_CONTRIBUTION","Annual contribution",0,1000000);KJE.InvestRateSlider("RATE_OF_RETURN","Expected rate of return");KJE.NumberSlider("AGE_OF_RETIREMENT","Age of retirement",10,70,0);KJE.PercentSlider("CURRENT_TAX_RATE","Current tax rate",0,50,2);KJE.PercentSlider("RETIREMENT_TAX_RATE","Retirement tax rate",0,50,2);KJE.Label("TOTAL_CONTRIBUTIONS","Total contributions");KJE.Checkbox("SAVE_TAX","Invest traditional tax-savings",true,"Invest any tax-savings generated by traditional contributions");KJE.Checkbox("CONTRIBUTE_MAX","Maximize contributions",false,"Increase future contributions to the maximum allowed");var g=KJE.gNewGraph(KJE.gCATEGORIES,"GRAPH1",true,false,KJE.colorList[1],KJE.parameters.get("MSG_GRAPH3","After-Tax Total At Retirement"));g._showItemLabel=true;g._showItemLabelOnTop=true;g._axisX._fSpacingPercent=0.3;g._grid._showYGridLines=false;g._legend._iOrientation=(a.TOP_RIGHT);g._axisX.setVisible(false);g._axisY.setVisible(true);g._bPopDetail=true;var f=KJE.gNewGraph(KJE.gLINE,"GRAPH2",true,true,KJE.colorList[1],KJE.parameters.get("MSG_GRAPH2","After-Tax Comparison"));f._legend._iOrientation=(a.TOP_RIGHT);f._titleXAxis.setText(KJE.parameters.get("MSG_GRAPH1","Age"));var k=KJE.parameters.get("MSG_DROPPER","Age and retirement plan information:");KJE.addDropper(new KJE.Dropper("INPUTS",true,k,k),KJE.colorList[0]);var j=KJE.parameters.get("MSG_DROPPER2_TITLE","Investment return and taxes:");var d=KJE.parameters.get("MSG_DROPPER2_CLOSETITLE","KJE1 return, KJE2 current tax rate, KJE3 tax during retirement");var c=function(){return j+KJE.subText(KJE.getKJEReplaced(d,e.RATE_OF_RETURN.getFormatted(),e.CURRENT_TAX_RATE.getFormatted(),e.RETIREMENT_TAX_RATE.getFormatted()),"KJECenter")};var i=new KJE.Dropper("INPUTS2",false,j,c);i.setBackground(KJE.colorList[0])};KJE.RothvsTraditional401k.prototype.setValues=function(b){var a=KJE.inputs.items;b.CURRENT_AGE=a.CURRENT_AGE.getValue();b.ANNUAL_CONTRIBUTION=a.ANNUAL_CONTRIBUTION.getValue();b.RATE_OF_RETURN=a.RATE_OF_RETURN.getValue();b.RETIREMENT_RATE_OF_RETURN=6;b.AGE_OF_RETIREMENT=a.AGE_OF_RETIREMENT.getValue();b.CURRENT_TAX_RATE=a.CURRENT_TAX_RATE.getValue();b.RETIREMENT_TAX_RATE=a.RETIREMENT_TAX_RATE.getValue();b.YEARS_OF_WITHDRAWALS=20;b.ANNUAL_PERIODS=12;b.SAVE_TAX=a.SAVE_TAX.getValue();b.bCONTRIBUTE_MAX=a.CONTRIBUTE_MAX.getValue();if(b.bCONTRIBUTE_MAX){a.ANNUAL_CONTRIBUTION.disable(true)}else{a.ANNUAL_CONTRIBUTION.enable()}};KJE.RothvsTraditional401k.prototype.refresh=function(f){var e=KJE;var d=KJE.gLegend;var b=KJE.inputs.items;var a=KJE.gGraphs[1];var c=KJE.gGraphs[0];KJE.setTitleTemplate();a.removeAll();a.setGraphCategories(f.cats);a.add(new KJE.gGraphDataSeries(f.DD_ROTH,this.MSG_GRAPH4,a.getColor(1)));a.add(new KJE.gGraphDataSeries(f.DD_V401K_AF_TAX,(f.SAVE_TAX?this.MSG_GRAPH5:this.MSG_GRAPH8),a.getColor(2)));a.paint();c.removeAll();c.setGraphCategories([this.MSG_GRAPH4,(f.SAVE_TAX?this.MSG_GRAPH5:this.MSG_GRAPH8)]);c.add(new KJE.gGraphDataSeries(f.DS_TOTAL2,this.MSG_GRAPH7,a.getColor(1)));c.paint();b.TOTAL_CONTRIBUTIONS.setText(e.dollars(f.TOTAL_CONTRIBUTIONS,0));b.ANNUAL_CONTRIBUTION.setValue(f.ANNUAL_CONTRIBUTION,true)};KJE.Default.CALC_TYPE_457=1;KJE.Default.CALC_TYPE_401k=0;KJE.Default.CALC_TYPE_403b=2;KJE.InputScreenText=" <div id=KJE-D-INPUTS><div id=KJE-P-INPUTS>Input information:</div></div> <div id=KJE-E-INPUTS > <div id='KJE-C-CURRENT_AGE'><input id='KJE-CURRENT_AGE' /></div> <div id='KJE-C-AGE_OF_RETIREMENT'><input id='KJE-AGE_OF_RETIREMENT' /></div> <div id='KJE-C-ANNUAL_CONTRIBUTION'><input id='KJE-ANNUAL_CONTRIBUTION' /></div> <div id='KJE-C-TOTAL_CONTRIBUTIONS'><div id='KJE-TOTAL_CONTRIBUTIONS'></div></div> <div id='KJE-C-SAVE_TAX'><input id='KJE-SAVE_TAX' type=checkbox name='SAVE_TAX' /></div> <div id='KJE-C-CONTRIBUTE_MAX'><input id='KJE-CONTRIBUTE_MAX' type=checkbox name='CONTRIBUTE_MAX' /></div> <div style=\"height:10px\"></div> </div> <div id=KJE-D-INPUTS2><div id=KJE-P-INPUTS2>Input information:</div></div> <div id=KJE-E-INPUTS2 > <div id='KJE-C-RATE_OF_RETURN'><input id='KJE-RATE_OF_RETURN' /></div> <div id='KJE-C-CURRENT_TAX_RATE'><input id='KJE-CURRENT_TAX_RATE' /></div> <div id='KJE-C-RETIREMENT_TAX_RATE'><input id='KJE-RETIREMENT_TAX_RATE' /></div> <div style=\"height:10px\"></div> </div> **GRAPH1** **GRAPH2** ";KJE.DefinitionText=" <div id='KJE-D-CURRENT_AGE' ><dt>Current age</dt><dd>Your current age.</dd></div> <div id='KJE-D-AGE_OF_RETIREMENT' ><dt>Age of retirement</dt><dd>Age you wish to retire. This calculator assumes that the year you retire, you do not make any contributions to your 401(k). So if you retire at age 65, your last contribution occurs when you are actually 64.</dd></div> <div id='KJE-D-ANNUAL_CONTRIBUTION' ><dt>Annual contribution</dt><dd>The amount you will contribute to a 401(k) each year. This calculator assumes that you make 12 equal contributions throughout the year at the beginning of each month. The annual maximum for 2016 is $18,000. If you are age 50 or over, a \"catch-up\" provision allows you to contribute even more to your 401(k). Employees age 50 or over can deposit an additional $6,000 into their 401(k) account. It is also important to note that employer contributions do not affect an employee's maximum annual contribution limit. Both the annual maximum and \"catch-up\" provisions are indexed for inflation. <p>It is important to note that some employees are subject to another form of contribution limits. Employees classified as \"Highly Compensated\" may be subject to contribution limits based on their employer's overall 401(k) participation. If you expect your salary to be $120,000 or more in 2016 or was $120,000 or more in 2015, you may need to contact your employer to see if these additional contribution limits apply to you. <p></dd></div> <div id='KJE-D-SAVE_TAX' ><dt>Invest traditional tax-savings</dt><dd>Check this box to invest any tax savings generated by contributions to a traditional 401(k). By investing your tax savings each year, you equalize the total cash flow between the two account types. For example, if you have a 25% income tax rate and contribute $1,000 to your retirement account, the actual cost after taxes would be $750 for the traditional contribution and $1,000 for the Roth contribution. If you do not wish to invest the difference, you are actually \"spending\" more per year with the Roth option and the end result will greatly favor a Roth-type savings plan. You may wish to leave this box unchecked if you have no ability or desire to create an additional investment account outside of your 401(k).</dd></div> <div id='KJE-D-CONTRIBUTE_MAX' ><dt>Maximize contributions</dt><dd>Check this box to increase all contributions to the maximum allowed each year. This will include future years that qualify for catch-up contributions. The annual maximum for 2016 is $18,000. When you reach age 50 or over, a \"catch-up\" provision increases the maximum by an additional $6,000.</dd></div> <div id='KJE-D-RATE_OF_RETURN' ><dt>Expected rate of return</dt><dd>The annual rate of return for your 401(k) account. This calculator assumes that your return is compounded annually and your deposits are made monthly. **ROR_DEFINITION**</dd></div> <div id='KJE-D-CURRENT_TAX_RATE' ><dt>Current tax rate</dt><dd>The current marginal income tax rate you expect to pay on your taxable investments. Use the table below to assist you in determining your current tax rate. **TAXTABLE_CURRENT_DEFINITION**</dd></div> <div id='KJE-D-RETIREMENT_TAX_RATE' ><dt>Retirement tax rate</dt><dd>The marginal tax rate you expect to pay on your investments at retirement.</dd></div> <div><dt>After tax total at retirement</dt><dd>For the Roth 401(k), this is the total value of the account. For the traditional 401(k), this is the sum of two parts: 1) The value of the account after you pay income taxes on all earnings and tax deductible contributions and 2) what you would have earned if you had invested (in an ordinary taxable account) any income tax savings. <!--MINISTER_NOTE--></dd></div> ";KJE.ReportText=' <!--HEADING "Roth vs traditional 401(k)" HEADING--> <h2 class=\'KJEReportHeader KJEFontHeading\'>MSG_GO</h2> RESULTS_MSG **GRAPH** <h2 class=\'KJEReportHeader KJEFontHeading\'>How was this calculated?</h2> <p><span class="KJEBold">Step 1</span>: First we found the value of a Roth 401(k) if you contributed ANNUAL_CONTRIBUTION per year for YEARS_UNTIL_RETIREMENT years earning an assumed RATE_OF_RETURN per year. This equaled TOTAL_ROTH. Since qualified withdrawals from a Roth 401(k) are not taxed, the total value remains TOTAL_ROTH. <p><span class="KJEBold">Step 2</span>: We then computed the totals for a traditional 401(k). Again we determined the value of ANNUAL_CONTRIBUTION per year for YEARS_UNTIL_RETIREMENT years earning an assumed RATE_OF_RETURN per year. This is the same amount as the Roth 401(k) total, V401K_TOTAL_BF_TAX. However, contributions and all earnings in a traditional 401(k) are taxable when they are withdrawn. After taxes, the value of your traditional 401(k) account would be V401K_TOTAL_AF_TAX. <!--SHOW_TAXABLE--><p><span class="KJEBold">Step 3</span>: Since you receive a current year tax deduction for any traditional 401(k) contributions, we need to determine the value of investing this tax savings and add this amount to the traditional 401(k) total. If we forget this step, our comparison will not be equal. We would, in effect, be contributing more to our Roth 401(k) than the traditional 401(k). If your tax savings were invested for YEARS_UNTIL_RETIREMENT years at an assumed rate of RATE_OF_RETURN, this returns a total of TOTAL_TAXABLE after taxes. <!--/SHOW_TAXABLE--> <div class=KJEReportTableDiv><table class=KJEReportTable><caption class=\'KJEHeaderRow KJEHeading\'>Results Summary</caption> <tr class=KJEFooterRow><td class="KJELabel KJECellBorder " ALIGN=CENTER>&nbsp;</td><td class="KJECellStrong KJECellBorder">Traditional 401(k)</td><td class="KJECellStrong">Roth 401(k)</td></tr> <tr class=KJEOddRow><td class="KJELabel KJECellBorder KJECell40" >Total contributions<BR>MSG_CONTRIBUTE_LBL</td><td class="KJECell KJECellBorder KJECell30" >TOTAL_CONTRIBUTIONS</td><td class="KJECell KJECell30" >TOTAL_CONTRIBUTIONS</td></tr> <tr class=KJEEvenRow><td class="KJELabel KJECellBorder" >Total before taxes</td><td class="KJECell KJECellBorder" >V401K_TOTAL_BF_TAX</td><td class="KJECell" >TOTAL_ROTH</td></tr> <!--SHOW_TAXABLE--><tr class=KJEOddRow><td class="KJELabel KJECellBorder" >Value of investing tax savings</td><td class="KJECell KJECellBorder" >+ TOTAL_TAXABLE</td><td class="KJECell" >+ 0</td></tr> <!--/SHOW_TAXABLE--> <tr class=KJEEvenRow><td class="KJELabel KJECellBorder" >Taxes for 401(k) at retirement</td><td class="KJECell KJECellBorder" >- V401K_TOTAL_TAXES</td><td class="KJECell" >- 0</td></tr> <tr class=KJEOddRow><td class="KJELabel KJECellBorder" >Value at retirement (age AGE_OF_RETIREMENT)</td><td class="KJECell KJECellBorder" >TOTAL_401K</td><td class="KJECell" >TOTAL_ROTH</td></tr> <tr class=KJEHeaderRow><th class=KJEHeading COLSPAN=3 ALIGN=CENTER>RESULTS_MSG</th></tr></table> </div> <div class=KJEReportTableDiv><table class=KJEReportTable><caption class=\'KJEHeaderRow KJEHeading\'>Input Summary</caption> <tr class=KJEOddRow><td class="KJELabel KJECellBorder KJECell40" >Annual contribution*<BR>MSG_CONTRIBUTE_LBL</td><td class="KJECell KJECellBorder KJECell20" >ANNUAL_CONTRIBUTION</td><td class="KJECell KJECellBorder KJECell20" >Current age</td><td class="KJECell KJECell20" >CURRENT_AGE</td></tr> <tr class=KJEEvenRow><td class="KJELabel KJECellBorder" >Years until retirement</td><td class="KJECell KJECellBorder" >YEARS_UNTIL_RETIREMENT</td><td class="KJECell KJECellBorder" >Age of retirement</td><td class="KJECell" >AGE_OF_RETIREMENT</td></tr> <tr class=KJEOddRow><td class="KJELabel KJECellBorder" >Expected rate of return</td><td class="KJECell KJECellBorder" >RATE_OF_RETURN</td><td class="KJECell KJECellBorder" >&nbsp;</td><td class="KJECell" >&nbsp;</td></tr> <tr class=KJEEvenRow><td class="KJELabel KJECellBorder" >Current tax rate</td><td class="KJECell KJECellBorder" >CURRENT_TAX_RATE</td><td class="KJECell KJECellBorder" >Retirement tax rate</td><td class="KJECell" >RETIREMENT_TAX_RATE</td></tr></table> </div> <div class=KJEInset> <P class=KJEFooter>*The annual maximum for 2016 is $18,000. If you are age 50 or over, a "catch-up" provision allows you to contribute even more to your 401(k). Employees age 50 or over can deposit an additional $6,000 into their 401(k) account. It is also important to note that employer contributions do not affect an employee\'s maximum annual contribution limit. </div> **GRAPH** <h2 class=\'KJEScheduleHeader KJEFontHeading\'>401(k) Balances by year</h2> **REPEATING GROUP** ';