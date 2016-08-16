KJE.Default.TaxRate=25;KJE.Default.TaxRateRetire=15;KJE.Default.TaxRateCapGain=15;KJE.Default.StateTaxRate=8;KJE.Default.IncomeTaxTableTaxYear="Use the table below to assist you in estimating your federal tax rate. <div class='KJEReportTableDiv'><table class='KJEReportTable KJEReportTableShrink'><caption class='KJEHeaderRow KJEHeading'>Filing Status and Income Tax Rates 2016<sup>*</sup></caption><thead><tr class=KJEFooterRow><th class='KJELabel KJECell10 KJECenter' style='vertical-align:bottom;'>Tax Rate</th><th class='KJELabel KJECell225 KJECenter' style='vertical-align:bottom;'>Married Filing Jointly or Qualified Widow(er)</th><th class='KJELabel KJECell225 KJECenter' style='vertical-align:bottom;'>Single</th><th class='KJELabel KJECell225 KJECenter' style='vertical-align:bottom;'>Head of Household</th><th class='KJELabel KJECell225 KJECenter' style='vertical-align:bottom;'>Married Filing Separately</th></tr></thead><tr class='KJEOddRow'><td class='KJELabel KJECellBorder KJELabelPad'>10%</td><td class='KJECell KJECellBorder'>$0 -&nbsp;$18,550</td><td class='KJECell KJECellBorder'>$0 -&nbsp;$9,275</td><td class='KJECell KJECellBorder'>$0 -&nbsp;$13,250</td><td class='KJECell'>$0 -&nbsp;$9,275</td></tr><tr class=KJEEvenRow style='margin-top:10px;'><td class='KJELabel KJECellBorder KJELabelPad'>15%</td><td class='KJECell KJECellBorder'>$18,550 -&nbsp;$75,300</td><td class='KJECell KJECellBorder'>$9,275 -&nbsp;$37,650</td><td class='KJECell KJECellBorder'>$13,250 -&nbsp;$50,400</td><td class='KJECell'>$9,275 -&nbsp;$37,650</td></tr><tr class=KJEOddRow><td class='KJELabel KJECellBorder KJELabelPad'>25%</td><td class='KJECell KJECellBorder'>$75,300 -&nbsp;$151,900</td><td class='KJECell KJECellBorder'>$37,650 -&nbsp;$91,150</td><td class='KJECell KJECellBorder'>$50,400 -&nbsp;$130,150</td><td class='KJECell'>$37,650 -&nbsp;$75,950</td></tr><tr class=KJEEvenRow><td class='KJELabel KJECellBorder KJELabelPad'>28%</td><td class='KJECell KJECellBorder'>$151,900 -&nbsp;$231,450</td><td class='KJECell KJECellBorder'>$91,150 -&nbsp;$190,150</td><td class='KJECell KJECellBorder'>$130,150 -&nbsp;$210,800</td><td class='KJECell'>$75,950 -&nbsp;$115,725</td></tr><tr class=KJEOddRow><td class='KJELabel KJECellBorder KJELabelPad'>33%</td><td class='KJECell KJECellBorder'>$231,450  -$413,350</td><td class='KJECell KJECellBorder'>$190,150 -&nbsp;$413,350</td><td class='KJECell KJECellBorder'>$210,800 -&nbsp;$413,350</td><td class='KJECell'>$115,725 -&nbsp;$206,675</td></tr><tr class=KJEEvenRow><td class='KJELabel KJECellBorder KJELabelPad'>35%</td><td class='KJECell KJECellBorder'>$413,350  -$466,950</td><td class='KJECell KJECellBorder'>$413,350 -&nbsp;$415,050</td><td class='KJECell KJECellBorder'>$413,350 -&nbsp;$441,000</td><td class='KJECell'>$206,675 -&nbsp;$233,475</td></tr><tr class='KJEOddRow'><td class='KJELabel KJECellBorder KJELabelPad'>39.6%</td><td class='KJECell KJECellBorder'>Over$466,950 </td><td class='KJECell KJECellBorder'>Over$415,050 </td><td class='KJECell KJECellBorder'>Over$441,000 </td><td class='KJECell'>Over$233,475 </td></tr></table><div align=center><br><sup>*</sup>Caution: Do not use these tax rate schedules to figure 2015 taxes. Use only to figure 2016 estimates. Source: 2015 Rev. Proc. 2015-61</div></div>";KJE.Default.IncomeTaxTableCurrent=KJE.Default.IncomeTaxTableTaxYear;KJE.definitions.set("**TAXTABLE_CURRENT_DEFINITION**",KJE.Default.IncomeTaxTableCurrent);KJE.definitions.set("**TAXTABLE_TAXYEAR_DEFINITION**",KJE.Default.IncomeTaxTableTaxYear);KJE.MortgageLoanCalculation=function(){this.bTERMINMONTHS=KJE.parameters.get("TERM_IN_MONTHS",false);this.MSG_YEAR_NUMBER=KJE.parameters.get("MSG_YEAR_NUMBER","Year Number");this.MSG_POP_PRINCIPAL=KJE.parameters.get("MSG_POP_PRINCIPAL","Total Principal for");this.MSG_POP_INTEREST=KJE.parameters.get("MSG_POP_INTEREST","Total Interest for");this.MSG_PRINCIPAL=KJE.parameters.get("MSG_PRINCIPAL","Principal");this.MSG_INTEREST=KJE.parameters.get("MSG_INTEREST","Interest");this.MSG_PRINCIPAL_BALANCE=KJE.parameters.get("MSG_PRINCIPAL_BALANCE","Principal Balance");this.MSG_POP_PRINCIPAL_NORMAL=KJE.parameters.get("MSG_POP_PRINCIPAL_NORMAL","Principal Balance for Normal Payments Year");this.MSG_POP_PRINCIPAL_PREPAY=KJE.parameters.get("MSG_POP_PRINCIPAL_PREPAY","Principal Balance for Prepayments Year");this.MSG_PREPAYMENTS=KJE.parameters.get("MSG_PREPAYMENTS","Prepayments");this.MSG_NORMAL_PAYMENTS=KJE.parameters.get("MSG_NORMAL_PAYMENTS","Normal");this.MSG_PREPAY_MESSAGE=KJE.parameters.get("MSG_PREPAY_MESSAGE","Your planned prepayment(s) will shorten your mortgage by PREPAY_SHORTEN_TERM.");this.MSG_RETURN_AMOUNT=KJE.parameters.get("MSG_RETURN_AMOUNT","A monthly payment of MONTHLY_PI at INTEREST_RATE for TERM years will give you a mortgage amount of LOAN_AMOUNT.");this.MSG_RETURN_PAYMENT=KJE.parameters.get("MSG_RETURN_PAYMENT","A loan amount of LOAN_AMOUNT at INTEREST_RATE for TERM years will give you a monthly payment (PI) of MONTHLY_PI.");this.MSG_ERROR_BALLOON=KJE.parameters.get("MSG_ERROR_BALLOON","Loan term must be less than the amortization term.");this.PITI_PERCENT=KJE.parameters.get("PITI_PERCENT",false);this.SHOW_PITI=KJE.parameters.get("SHOW_PITI",false);this.USE_OTHER_FEES_AMOUNT=KJE.parameters.get("USE_OTHER_FEES_AMOUNT",true);this.ADJUSTABLE_RATE=false;this.sSchedule=new KJE.Repeating();this.sAdjSchedule=null};KJE.MortgageLoanCalculation.prototype.clear=function(){this.ADJUSTABLE_RATE_CAP=0;this.ADJUSTABLE_RATE_FEQ=12;this.ADJUSTABLE_RATE_FIXED=0;this.ADJUSTABLE_RATE_INCR=0;this.BALLOON_PAYMENT=0;this.DISCOUNT_POINTS_PERCENT=0;this.FEDERAL_TAX_RATE=0;this.INFLATION_RATE=3;this.INTEREST_ONLY=false;this.INTEREST_RATE=0;this.LOAN_AMOUNT=0;this.MARGINAL_TAX_RATE=0;this.MSG_TERM="";this.ORIGINATION_FEES_PERCENT=0;this.OTHER_FEES=0;this.OTHER_FEES_RATE=0;this.PAYMENT_CALC=1;this.PREPAY_AMOUNT=0;this.PREPAY_BALLOON_PAYMENT=0;this.PREPAY_STARTS_WITH=1;this.PREPAY_TYPE=KJE.Default.PREPAY_NONE;this.PURCHASE_PRICE=0;this.RATE_INDEX=0;this.RATE_INDEX_MARGIN=0;this.RECAST_TO_AMORTIZE=1000000;this.SAVINGS_RATE=0;this.STATE_TAX_RATE=0;this.TERM=0;this.TERM_BALLOON=0;this.YEARS_IN_HOME=0;this.YEARLY_HOME_INSURANCE=0;this.YEARLY_PROPERTY_TAXES=0;this.BY_YEAR=true};KJE.MortgageLoanCalculation.prototype.calculate=function(h){var aH=KJE;if(this.PITI_PERCENT&&this.SHOW_PITI){this.YEARLY_PROPERTY_TAXES=aH.round((this.YEARLY_PROPERTY_TAXES/100)*this.LOAN_AMOUNT);this.YEARLY_HOME_INSURANCE=aH.round((this.YEARLY_HOME_INSURANCE/100)*this.LOAN_AMOUNT)}var U=this.ADJUSTABLE_RATE_CAP;var aP=this.ADJUSTABLE_RATE_FEQ;var m=this.ADJUSTABLE_RATE_FIXED;var L=this.ADJUSTABLE_RATE_INCR;var b=this.ADJUSTABLE_RATE;var aK=this.BALLOON_PAYMENT;var bk=this.bTERMINMONTHS;var W=this.DISCOUNT_POINTS_PERCENT;var aW=this.FEDERAL_TAX_RATE;var C=this.INFLATION_RATE;var ac=this.INTEREST_ONLY;var a2=this.INTEREST_RATE;var a5=this.LOAN_AMOUNT;var O=this.MARGINAL_TAX_RATE;var at=this.ORIGINATION_FEES_PERCENT;var N=this.OTHER_FEES_RATE;var X=this.OTHER_FEES;var aL=this.PAYMENT_CALC;var P=this.PREPAY_AMOUNT;var d=this.PREPAY_BALLOON_PAYMENT;var a9=this.PREPAY_STARTS_WITH;var w=this.PREPAY_TYPE;var ae=this.PURCHASE_PRICE;var aC=this.RATE_INDEX_MARGIN;var t=this.RATE_INDEX;var aM=this.RECAST_TO_AMORTIZE;var k=this.SAVINGS_RATE;var Y=this.STATE_TAX_RATE;var ax=this.TERM_BALLOON;var V=this.TERM;var H=this.YEARLY_HOME_INSURANCE;var aF=this.YEARLY_PROPERTY_TAXES;var aj=this.YEARS_IN_HOME;var al=this.BY_YEAR;var M="";var R=0;var j="";var a7=0;var bh=0;var D=0;var p=0;var ah=0;var aB=0;var bc=0;var aQ=0;var ak=0;var aT=0;var a8=0;var l=0;var o=0;var T=0;var f=0;var aI=0;var be;var e=0;var Q=0;var a1=0;var bn="";var ag=0;var aU=0;var bg=0;var av=0;var a6=0;var ba=0;var bf=0;var bt=0;var ay=0;var bi=0;var bd=0;var c=0;var bb=0;var S=0;var af=0;var g=0;if(bk){this.MONTHS=af=V%12;this.TERM=V=Math.floor(V/12)}var y=this.TOTAL_MONTHS=af+V*12;if(O==0){O=((Y/100)*(1-aW/100))*100+aW}if(aL==0){if(ac){a5=aH.round(l/(a2/1200),2)}else{a5=aH.round(KJE.PV(a2/1200,V*12+af,l),2)}M=this.MSG_RETURN_AMOUNT}else{if(ac){l=aH.round((a2/1200*a5),2)}else{l=aH.round(KJE.PMT(a2/1200,V*12+af,a5),2)}M=this.MSG_RETURN_PAYMENT}if(aj==0){aj=V+(af/12)}else{if(aj>V){aj=V+(af/12)}}var I=aj*12;var ad=true;if(ae==0){ae=a5;ad=false}if(!this.USE_OTHER_FEES_AMOUNT){X=aH.round((N/100)*a5,2)}D=aH.round((W/100)*a5,2);aI=aH.round((at/100)*a5,2);bt=D+aI+X;aT=(a5/ae);a8=aH.round(H/12,2);f=aH.round(aF/12,2);o=a8+f+l;ah=aH.round((a2/1200)*a5,2);aB=(ac?0:l-ah);var v=a2/1200;var aS=O/100;var B=k/1200;g=(t+aC)/100;if(b&&g!=a2/100&&g!=0){bi=KJE.MortgageLoanCalculation.APRAdjustable(V*12+af,a5,bt,a2/100,m,aP,g,L/100,U)}else{bi=KJE.APR(V*12+af,l,v,a5,bt)*12}bd=aH.round(KJE.PMT(v,V*12+af,a5+bt),2);c=a5+bt;bf=(a2/100)*(1-(aS*(a5>1000000?1000000/a5:1)));bb=(bi)*(1-(aS*(a5>1000000?1000000/a5:1)));ay=0;ak=0;var aA=false;if(ax>0){if(ax>V){throw this.MSG_ERROR_BALLOON}aA=true}if(ac&&aM<ax){ax=V;aA=true}var aE=Math.round(aA?ax:V)+1;var az=0;var aJ=this.DS_PRINCIPAL_BAL=KJE.FloatArray(aE);var aD=this.DS_PREPAY_PRINCIPAL_BAL=KJE.FloatArray(aE);var aY=this.DS_INTEREST_PAID=KJE.FloatArray(aE);var a4=this.DS_PAYMENTS=KJE.FloatArray(aE);var z=new Array(aE);var x=true;var a0=aF;av=(w==KJE.Default.PREPAY_ONETIME&&a9==0?P:0);var bm=a5-av;var aX=0;var aG=0;var br=0;var E=0;var ar=0;var aZ=(ac?ah:l);var bj=0;var J=0;var aR=a5;var aw=0;var aq=0;var A=0;var bs=0;var K=0;var Z=l;var a3=0;var bp=0;var bl=0;var u=0;var aa=0;var bq=0;if(w==KJE.Default.PREPAY_NONE){x=false}if(a9==0&&w!=KJE.Default.PREPAY_ONETIME){a9=1}var ap=0;z[ap]="0";aD[ap]=bm;aJ[ap]=a5;aY[ap]=0;a4[ap]=0;ap+=1;if(h){var am=this.sSchedule;am.clearRepeat();if(x){am.addHeader("&nbsp;",{sCell:KJE._sHeadingUnderline,sContent:am.sReportCol("Regular Payment Schedule",10),sFormat:"COLSPAN=3"},{sCell:KJE._sHeadingUnderline,sContent:am.sReportCol("Prepayment Payment Schedule",11),sFormat:"COLSPAN=3"})}if(!al&&x){am.addHeader(am.sReportCol("<BR><BR>Nbr",1),am.sReportCol("<BR><BR>Payment",2),am.sReportCol("<BR><BR>Interest",4),am.sReportCol("Ending<BR>Principal<BR>Balance",5),am.sReportCol("<BR><BR>Payment",2),am.sReportCol("<BR><BR>Interest",4),am.sReportCol("Ending<BR>Principal<BR>Balance",5))}else{if(!al&&!x){am.addHeader(am.sReportCol("<BR><BR>Nbr",1),am.sReportCol("<BR><BR>Payment",2),am.sReportCol("<BR><BR>Principal",3),am.sReportCol("<BR><BR>Interest",4),am.sReportCol("Ending<BR>Principal<BR>Balance",5))}else{if(al&&x){am.addHeader(am.sReportCol("<BR><BR>Yr ",6),am.sReportCol("<BR>Total<BR>Payments",7),am.sReportCol("<BR>Interest<BR>Paid",8),am.sReportCol("Ending<BR>Principal<BR>Balance",5),am.sReportCol("<BR>Total<BR>Payments",7),am.sReportCol("<BR>Interest<BR>Paid",8),am.sReportCol("Ending<BR>Principal<BR>Balance",5))}else{am.addHeader(am.sReportCol("<BR><BR>Year",6),am.sReportCol("<BR>Total<BR>Payments",7),am.sReportCol("<BR>Principal<BR>Paid",9),am.sReportCol("<BR>Interest<BR>Paid",8),am.sReportCol("Ending<BR>Principal<BR>Balance",5))}}}if(x){am.addRepeat("&nbsp;","&nbsp;","&nbsp;",aH.dollars(aR,2),(w==KJE.Default.PREPAY_ONETIME&&a9==0?aH.dollars(P,2):""),"&nbsp;","&nbsp;",aH.dollars(bm,2))}else{am.addRepeat("&nbsp;","&nbsp;","&nbsp;","&nbsp;",aH.dollars(aR,2))}}a7=l;var aO=l;var ab=l;var bo=a2/100;var ao=a2/100;var aV=0;if(b&&L!=0){if(this.sAdjSchedule==null){this.sAdjSchedule=new KJE.Repeating()}var G=this.sAdjSchedule;G.clearRepeat();G.addHeader(G.sReportCol("Payment Number",12),G.sReportCol("Interest Rate",13),G.sReportCol("Monthly Payment",14));G.addRepeat("1",aH.percent(ao,2),aH.dollars(l,2))}var F=(aA?ax*12:V*12+af);for(var au=1;au<=F;au++){az=au-1;aZ=aO;Z=ab;bj=0;a3=0;if(x&&(a9<=au)){if(w==KJE.Default.PREPAY_ONETIME&&a9==au){bj=P}else{if(w==KJE.Default.PREPAY_YEARLY){if(((au-a9)%12)==0){bj=P}}else{if(w==KJE.Default.PREPAY_MONTHLY){bj=P}}}}aw=aH.round(v*aR,2);if(ac&&au<=aM){Z=aw}bl=aH.round(v*(aR>1000000?1000000:aR),2);aq=(ac&&au<aM?0:Z-aw);aR-=aq;if(aR==0){Z=0;aq=0;aw=0}else{if(aR<0||(aR>0.005&&F==au&&!aA)){aq+=aR;aR=0;Z=aq+aw}else{if(F==au&&!aA){aR=0}}}aX=aH.round(v*bm,2);if(ac&&au<=aM){aZ=aX}u=aH.round(v*(bm>1000000?1000000:bm),2);if(ac&&au<aM){if(bm==0){aZ=0;aG=0;aX=0;bj=0}else{aG=aZ-aX;bm-=aG+bj;if(bm<0){bj+=bm;bm=0}}}else{aG=aZ-aX;bm-=aG+bj;if(bm==0){aZ=0;aG=0;aX=0;bj=0}else{if(bm<0){bj+=bm;if(bj<0){aG+=bj;bj=0}bm=0;aZ=aG+aX}else{if(bm>0.005&&F==au&&!aA){aG+=bm;bm=0;aZ=aG+aX}else{if(F==au&&!aA){bm=0}}}}}if(aZ<0){aZ=0}if(bm==0&&aU==0){ag=au;aU=V*12+af-au}br+=aX;aa+=u;E+=aG;ar+=aZ;J+=bj;av+=aZ+bj;Q+=aX;A+=aw;bq+=bl;bs+=aq;K+=Z;ay+=Z;ak+=aw;if((au%12)==0){if(au==12){bc=A;e=br;aQ=(O/100*(D+bq+a0));a3=aQ}else{a0*=1+C;a3=((O/100)*(bq+a0))}bp+=a3;bq=0;aa=0;a3=0}if(aA&&F==au){aK=aR+Z;aR=0;d=bm+aZ+bj;bm=0;ay-=Z;av-=bj+aZ}if(!al&&h){if(x){am.addRepeat(aH.number(au),aH.dollars((aA&&F==au?aK:Z),2),aH.dollars(aw,2),aH.dollars(aR,2),aH.dollars((aA&&F==au?d:bj+aZ),2),aH.dollars(aX,2),aH.dollars(bm,2))}else{am.addRepeat(aH.number(au),aH.dollars((aA&&F==au?aK:Z),2),aH.dollars((aA&&F==au?aK-aw:aq),2),aH.dollars(aw,2),aH.dollars(aR,2))}}if((au%12)==0){z[ap]=""+ap;if(aA&&F==au){aD[ap]=d;aJ[ap]=aK}else{aD[ap]=bm;aJ[ap]=aR}aY[ap]=A;a4[ap]=(aA&&F==au?aK-Z+K:K);ap+=1;if(al&&h){if(x){am.addRepeat(aH.number(au/12),aH.dollars((aA&&F==au?aK-Z+K:K),2),aH.dollars(A,2),aH.dollars(aR,2),aH.dollars((aA&&F==au?d-bj-aZ+ar+J:ar+J),2),aH.dollars(br,2),aH.dollars(bm,2))}else{am.addRepeat(aH.number((au/12)),aH.dollars((aA&&F==au?aK-Z+K:K),2),aH.dollars((aA&&F==au?aK+bs-aw-aq:bs),2),aH.dollars(A,2),aH.dollars(aR,2))}}A=0;bq=0;bs=0;K=0;br=0;aa=0;E=0;ar=0;J=0}if((au==aM)||((au<m?1:(au-m)%aP)==0&&au!=1&&b&&au!=V*12+af&&L!=0&&au>=m)){ao+=L/100;if(ao>U/100){ao=U/100}if(ao<0.02){ao=0.02}if(ao!=bo||(au==aM)){bo=ao;v=ao/12;aO=aH.round(KJE.PMT(v,V*12+af-au,bm),2);ab=aH.round(KJE.PMT(v,V*12+af-au,aR),2);if(R==0){R=ab}S=ab;if(a7<ab){a7=ab}if(b&&L!=0){G.addRepeat(au,aH.percent(ao,2),aH.dollars(ab,2))}}}}if(b&&L!=0){j=G.getRepeat();G.clearRepeat()}this.PREPAY_SHORTEN_TOTAL_MONTHS=aU;bg=(aU/12);aU=(aU%12);bn=this.MSG_PREPAY_MESSAGE;bh=(bp/(V+af/12));p=aR;be=bm;av=Q+a5-be;ay=ak+a5-p;var an=1;if(x){an=2}var aN=this.DS_INTEREST=new Array(an);var a=this.DS_PRINCIPAL=new Array(an);var ai=this.totalpaid_cats=new Array(an);aN[0]=ak;a[0]=a5-p;ai[0]=this.MSG_NORMAL_PAYMENTS;if(x){aN[1]=Q;a[1]=a5-be;ai[1]=this.MSG_PREPAYMENTS;a1=ak-Q}this.cats=z;this.sReturnMessage=M;this.MARGINAL_TAX_RATE=O;this.ADJUSTABLE_AFTER_FIRST_ADJ=R;this.ADJUSTABLE_PAYMENT_AMTS=j;this.ADJUSTABLE_RATE_HIGHEST=a7;this.AVG_TAX_SAVINGS=bh;this.DISCOUNT_POINTS_AMT=D;this.ENDING_BALANCE=p;this.FIRST_MONTH_INTEREST=ah;this.FIRST_MONTH_PRINCIPAL=aB;this.FIRST_YEAR_INTEREST=bc;this.FIRST_YEAR_TAX_SAVINGS=aQ;this.FULLY_INDEX_RATE=g;this.FULLY_INDEXED_PAYMENT=S;this.INTEREST_PAID=ak;this.LOAN_APR=bi;this.LOAN_APR_AFT=bb;this.LOAN_APR_AMOUNT=c;this.LOAN_APR_PAYMENT=bd;this.LOAN_TO_VALUE=aT;this.MONTHLY_HOME_INSURANCE=a8;this.MONTHLY_PI=l;this.MONTHLY_PITI=o;this.MONTHLY_PROPERTY_TAXES=f;this.MONTHS=af;this.ORIGINATION_FEES_AMT=aI;this.PREPAY_ENDING_BALANCE=be;this.PREPAY_FIRST_YEAR_INTEREST=e;this.PREPAY_INTEREST_PAID=Q;this.PREPAY_INTEREST_SAVINGS=a1;this.PREPAY_MESSAGE=bn;this.PREPAY_PAYOFF_MONTHS=ag;this.PREPAY_SHORTEN_MONTHS=aU;this.PREPAY_SHORTEN_YEARS=bg;this.PREPAY_TOTAL_OF_PAYMENTS=av;this.PREPAY_TOTAL_VALUE=a6;this.PREPAY_TOTAL_VALUE_AFTX=ba;this.TAX_ADJ_RATE=bf;this.TOTAL_CLOSING_COSTS=bt;this.TOTAL_OF_PAYMENTS=ay;this.OTHER_FEES=X;this.BALLOON_PAYMENT=aK;this.PREPAY_BALLOON_PAYMENT=d;this.REGULAR_PAYMENTS=aH.input(this.TERM_BALLOON*12-1)};KJE.MortgageLoanCalculation.prototype.formatReport=function(a){var b=KJE;var c=a;c=KJE.replace("FIXED_YEARS",b.number(this.ADJUSTABLE_RATE_FIXED/12),c);c=KJE.replace("ADJUSTABLE_YEARS",b.number(this.TERM+this.MONTHS/12-this.ADJUSTABLE_RATE_FIXED/12),c);c=KJE.replace("RECAST_TO_AMORTIZE_YEARS",b.number(this.RECAST_TO_AMORTIZE/12),c);c=KJE.replace("RECAST_TO_AMORTIZE",b.number(this.RECAST_TO_AMORTIZE),c);c=KJE.replace("REMAIN_AFTER_AMORTIZE",b.number(this.TERM*12+this.MONTHS-this.RECAST_TO_AMORTIZE),c);c=KJE.replace("MSG_TERM",this.MSG_TERM,c);c=KJE.replace("RESULT_MESSAGE",this.sReturnMessage,c);c=KJE.replace("YEARS_IN_HOME",b.number(this.YEARS_IN_HOME),c);c=KJE.replace("YEARLY_PROPERTY_TAXES",b.dollars(this.YEARLY_PROPERTY_TAXES,2),c);c=KJE.replace("YEARLY_HOME_INSURANCE",b.dollars(this.YEARLY_HOME_INSURANCE,2),c);c=KJE.replace("TOTAL_CLOSING_COSTS",b.dollars(this.TOTAL_CLOSING_COSTS,2),c);c=KJE.replace("TERM_BALLOON",b.number(this.TERM_BALLOON),c);if(this.MONTHS>0){c=KJE.replace("TERM",b.number(this.TERM*12+this.MONTHS),c);c=KJE.replace("years","months",c)}else{c=KJE.replace("TERM",b.number(this.TERM),c)}c=KJE.replace("TAX_ADJ_RATE",b.percent(this.TAX_ADJ_RATE,3),c);c=KJE.replace("SAVINGS_RATE",b.percent(this.SAVINGS_RATE/100,3),c);c=KJE.replace("PURCHASE_PRICE",b.dollars(this.PURCHASE_PRICE,2),c);c=KJE.replace("ADJUSTABLE_RATE_FEQ",b.number(this.ADJUSTABLE_RATE_FEQ),c);c=KJE.replace("ADJUSTABLE_RATE_INCR",b.percent(this.ADJUSTABLE_RATE_INCR/100,2),c);c=KJE.replace("ADJUSTABLE_RATE_CAP",b.percent(this.ADJUSTABLE_RATE_CAP/100,3),c);c=KJE.replace("ADJUSTABLE_PAYMENT_AMTS",this.ADJUSTABLE_PAYMENT_AMTS,c);c=KJE.replace("ADJUSTABLE_RATE_HIGHEST",b.dollars(this.ADJUSTABLE_RATE_HIGHEST,2),c);c=KJE.replace("ADJUSTABLE_AFTER_FIRST_ADJ",b.dollars(this.ADJUSTABLE_AFTER_FIRST_ADJ,2),c);c=KJE.replace("ADJUSTABLE_RATE_FIXED",b.number(this.ADJUSTABLE_RATE_FIXED),c);c=KJE.replace("RATE_INDEX_MARGIN",b.percent(this.RATE_INDEX_MARGIN/100,3),c);c=KJE.replace("RATE_INDEX",b.percent(this.RATE_INDEX/100,3),c);c=KJE.replace("ADJUSTABLE_RATE",b.yesno(this.ADJUSTABLE_RATE),c);c=KJE.replace("REGULAR_PAYMENTS",this.REGULAR_PAYMENTS,c);if(this.PREPAY_TYPE==KJE.Default.PREPAY_NONE){c=KJE.replace("PREPAY_MESSAGE","",c);c=KJE.replace("PREPAY_TYPE",this.PREPAY_TYPE,c);c=KJE.replace("PREPAY_TOTAL_VALUE_AFTX","",c);c=KJE.replace("PREPAY_TOTAL_VALUE","",c);c=KJE.replace("PREPAY_TOTAL_OF_PAYMENTS","",c);c=KJE.replace("PREPAY_SHORTEN_TERM","",c);c=KJE.replace("PREPAY_STARTS_WITH","",c);c=KJE.replace("PREPAY_SHORTEN_YEARS","",c);c=KJE.replace("PREPAY_SHORTEN_MONTHS","",c);c=KJE.replace("PREPAY_INTEREST_SAVINGS","",c);c=KJE.replace("PREPAY_INTEREST_PAID","",c);c=KJE.replace("PREPAY_FIRST_YEAR_INTEREST","",c);c=KJE.replace("PREPAY_AMOUNT","",c);c=KJE.replace("PREPAY_ENDING_BALANCE","",c);c=KJE.replace("PREPAY_BALLOON_PAYMENT","",c);c=KJE.replace("PREPAY_PAYOFF_PERIODS","",c)}else{c=KJE.replace("PREPAY_MESSAGE",this.PREPAY_MESSAGE,c);c=KJE.replace("PREPAY_TYPE",KJE.Default.PREPAY_PERIODS[this.PREPAY_TYPE],c);c=KJE.replace("PREPAY_TOTAL_VALUE_AFTX",b.dollars(this.PREPAY_TOTAL_VALUE_AFTX,2),c);c=KJE.replace("PREPAY_TOTAL_VALUE",b.dollars(this.PREPAY_TOTAL_VALUE,2),c);c=KJE.replace("PREPAY_TOTAL_OF_PAYMENTS",b.dollars(this.PREPAY_TOTAL_OF_PAYMENTS,2),c);c=KJE.replace("PREPAY_STARTS_WITH",b.number(this.PREPAY_STARTS_WITH),c);c=KJE.replace("PREPAY_SHORTEN_TERM",KJE.getTermLabel(this.PREPAY_SHORTEN_TOTAL_MONTHS),c);c=KJE.replace("PREPAY_SHORTEN_YEARS",b.number(this.PREPAY_SHORTEN_YEARS),c);c=KJE.replace("PREPAY_SHORTEN_MONTHS",b.number(this.PREPAY_SHORTEN_MONTHS),c);c=KJE.replace("PREPAY_INTEREST_SAVINGS",b.dollars(this.PREPAY_INTEREST_SAVINGS,2),c);c=KJE.replace("PREPAY_INTEREST_PAID",b.dollars(this.PREPAY_INTEREST_PAID,2),c);c=KJE.replace("PREPAY_FIRST_YEAR_INTEREST",b.dollars(this.PREPAY_FIRST_YEAR_INTEREST,2),c);c=KJE.replace("PREPAY_AMOUNT",b.dollars(this.PREPAY_AMOUNT,2),c);c=KJE.replace("PREPAY_ENDING_BALANCE",b.dollars(this.PREPAY_ENDING_BALANCE,2),c);c=KJE.replace("PREPAY_BALLOON_PAYMENT",b.dollars(this.PREPAY_BALLOON_PAYMENT,2),c);c=KJE.replace("PREPAY_PAYOFF_PERIODS",KJE.getTermLabel(this.PREPAY_PAYOFF_MONTHS),c)}c=KJE.replace("OTHER_FEES",b.dollars(this.OTHER_FEES,2),c);c=KJE.replace("ORIGINATION_FEES_PERCENT",b.percent(this.ORIGINATION_FEES_PERCENT/100,2),c);c=KJE.replace("ORIGINATION_FEES_AMT",b.dollars(this.ORIGINATION_FEES_AMT,2),c);c=KJE.replace("MONTHLY_PROPERTY_TAXES",b.dollars(this.MONTHLY_PROPERTY_TAXES,2),c);c=KJE.replace("MONTHLY_PITI",b.dollars(this.MONTHLY_PITI,2),c);c=KJE.replace("MONTHLY_PI",b.dollars(this.MONTHLY_PI,2),c);c=KJE.replace("MONTHLY_HOME_INSURANCE",b.dollars(this.MONTHLY_HOME_INSURANCE,2),c);c=KJE.replace("MARGINAL_TAX_RATE",b.percent(this.MARGINAL_TAX_RATE/100,2),c);c=KJE.replace("FEDERAL_TAX_RATE",b.percent(this.FEDERAL_TAX_RATE/100,2),c);c=KJE.replace("STATE_TAX_RATE",b.percent(this.STATE_TAX_RATE/100,2),c);c=KJE.replace("LOAN_TO_VALUE",b.percent(this.LOAN_TO_VALUE,2),c);c=KJE.replace("LOAN_APR_AFT",b.percent(this.LOAN_APR_AFT,3),c);c=KJE.replace("LOAN_APR_PAYMENT",b.dollars(this.LOAN_APR_PAYMENT,2),c);c=KJE.replace("LOAN_APR_AMOUNT",b.dollars(this.LOAN_APR_AMOUNT,2),c);c=KJE.replace("LOAN_APR",b.percent(this.LOAN_APR,3),c);c=KJE.replace("LOAN_AMOUNT",b.dollars(this.LOAN_AMOUNT,2),c);c=KJE.replace("INTEREST_RATE",b.percent(this.INTEREST_RATE/100,3),c);c=KJE.replace("INTEREST_PAID",b.dollars(this.INTEREST_PAID,2),c);c=KJE.replace("INFLATION_RATE",b.percent(this.INFLATION_RATE/100,2),c);c=KJE.replace("FIRST_YEAR_TAX_SAVINGS",b.dollars(this.FIRST_YEAR_TAX_SAVINGS,2),c);c=KJE.replace("FIRST_YEAR_INTEREST",b.dollars(this.FIRST_YEAR_INTEREST,2),c);c=KJE.replace("FIRST_MONTH_PRINCIPAL",b.dollars(this.FIRST_MONTH_PRINCIPAL,2),c);c=KJE.replace("FIRST_MONTH_INTEREST",b.dollars(this.FIRST_MONTH_INTEREST,2),c);c=KJE.replace("DISCOUNT_POINTS_PERCENT",b.number(this.DISCOUNT_POINTS_PERCENT,2),c);c=KJE.replace("DISCOUNT_POINTS_AMT",b.dollars(this.DISCOUNT_POINTS_AMT,2),c);c=KJE.replace("AVG_TAX_SAVINGS",b.dollars(this.AVG_TAX_SAVINGS,2),c);c=KJE.replace("TOTAL_OF_PAYMENTS",b.dollars(this.TOTAL_OF_PAYMENTS,2),c);c=KJE.replace("ENDING_BALANCE",b.dollars(this.ENDING_BALANCE,2),c);c=KJE.replace("BALLOON_PAYMENT",b.dollars(this.BALLOON_PAYMENT,2),c);c=KJE.replace("FULLY_INDEXED_PAYMENT",b.dollars(this.FULLY_INDEXED_PAYMENT,2),c);c=KJE.replace("INTEREST_ONLY",b.yesno(this.INTEREST_ONLY?1:0),c);c=KJE.replace("CHECKBOX_BY_MONTH",(this.BY_YEAR?"":"CHECKED"),c);c=KJE.replace("CHECKBOX_BY_YEAR",(this.BY_YEAR?"CHECKED":""),c);c=c.replace("**REPEATING GROUP**",this.sSchedule.getRepeat());this.sSchedule.clearRepeat();return c};KJE.MortgageLoanCalculation.prototype.getCategories=function(){return this.cats};KJE.MortgageLoanCalculation.prototype.getAmountPaidCategories=function(){return this.totalpaid_cats};KJE.MortgageLoanCalculation.APRAdjustable=function(t,q,d,k,s,j,c,f,l){var b=q;var p=k/12;var r=p;var h=KJE.PMT(p,t,b);var g=0;var e=new Array();e[0]=Math.round(100*(-q+d));for(var o=1;o<=t;o++){b-=h-(p*b);g+=h;e[o]=Math.round(100*h);if((o<s?1:(o-s)%j)==0&&o!=1&&o!=t){var m=c/12;if(m>(r+f)){m=r+f}if(m>l/12){m=l/12}if(m!=r){r=m;p=m;h=KJE.PMT(p,t-o,b)}}}var a=(c>k?c:k);return(KJE.MortgageLoanCalculation.IRR(e,a/12)*12)};KJE.MortgageLoanCalculation.IRR=function(f,e){var c=e/2;var b;var d=f.length;while(true){b=0;for(var a=0;a<d;a++){b+=f[a]/Math.pow((1+e),a)}if(b>-1&&b<1){break}e+=(b>0?c:-c);c=c/2}return e};KJE.Default.PREPAY_NONE=0;KJE.Default.PREPAY_WEEKLY=1;KJE.Default.PREPAY_BIWEEKLY=2;KJE.Default.PREPAY_2XMONTHLY=3;KJE.Default.PREPAY_MONTHLY=4;KJE.Default.PREPAY_YEARLY=5;KJE.Default.PREPAY_ONETIME=6;KJE.Default.PREPAY_FREQUENCY=[0,52,26,24,12,1,0];KJE.Default.getPrepayDrop=function(c,b,g){KJE.Default.PREPAY_PERIOD_IDs=KJE.parameters.get("ARRAY_PREPAY_PERIOD_ID",[KJE.Default.PREPAY_NONE,KJE.Default.PREPAY_MONTHLY,KJE.Default.PREPAY_YEARLY,KJE.Default.PREPAY_ONETIME]);KJE.Default.PREPAY_PERIODS=KJE.parameters.get("ARRAY_PREPAY_PERIODS",[KJE.parameters.get("MSG_PREPAY_NONE","none"),"Weekly","bi-weekly","semi-monthly",KJE.parameters.get("MSG_PREPAY_MONTHLY","monthly"),KJE.parameters.set("MSG_PREPAY_YEARLY","yearly"),KJE.parameters.get("MSG_PREPAY_ONETIME","one-time")]);var a=KJE.Default.PREPAY_PERIOD_IDs;var f=a.length;var e=KJE.Default.PREPAY_PERIODS;var d=new Array(f);for(i=0;i<f;i++){d[i]=e[a[i]]}return KJE.getDropBox(c,KJE.parameters.get(c,(!b?KJE.Default.PAY_LOAN_IDs:b)),a,d,g)};KJE.MortgageCompareCalc=function(){this.TERM15_TERM=KJE.parameters.get("TERM15_TERM",15);this.TERM30_TERM=KJE.parameters.get("TERM30_TERM",30);this.RC=new KJE.MortgageLoanCalculation();this.DS_TERM15_PAYMENT=KJE.FloatArray(1);this.DS_TERM30_PAYMENT=KJE.FloatArray(1);this.cats=["0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30"];this.payments=[KJE.parameters.get("MSG_AXIS_LABEL","Monthly Principal & Interest Payment")];this.sSchedule=new KJE.Repeating()};KJE.MortgageCompareCalc.prototype.clear=function(){this.TERM15_INTEREST_RATE=0;this.TERM30_INTEREST_RATE=0;this.MARGINAL_TAX_RATE=0;this.LOAN_AMOUNT=0;this.YEARS_IN_HOME=0;this.SAVINGS_RATE=0;this.BY_YEAR=1};KJE.MortgageCompareCalc.prototype.calculate=function(l){var c=KJE;var g=this.RC;var j=this.TERM15_INTEREST_RATE;var a=this.TERM30_INTEREST_RATE;var d=this.MARGINAL_TAX_RATE;var e=this.LOAN_AMOUNT;var k=this.YEARS_IN_HOME;var h=this.SAVINGS_RATE;var f=this.BY_YEAR;g.clear();g.bWithSchedule=true;g.TERM=this.TERM15_TERM;g.INTEREST_RATE=j;g.MARGINAL_TAX_RATE=d;g.LOAN_AMOUNT=e;g.YEARS_IN_HOME=k;g.SAVINGS_RATE=h;g.BY_YEAR=f;g.calculate();this.TERM15_INTEREST_PAID=g.INTEREST_PAID;this.TERM15_TOTAL_OF_PAYMENTS=g.TOTAL_OF_PAYMENTS;this.TERM15_MONTHLY_PI=g.MONTHLY_PI;this.TERM15_FIRST_MONTH_INTEREST=g.FIRST_MONTH_INTEREST;this.TERM15_FIRST_MONTH_PRINCIPAL=g.FIRST_MONTH_PRINCIPAL;this.TERM15_AVG_TAX_SAVINGS=g.AVG_TAX_SAVINGS;this.TERM15_TAX_ADJ_RATE=g.TAX_ADJ_RATE;this.TERM15_FIRST_YEAR_INTEREST=g.FIRST_YEAR_INTEREST;this.TERM15_FIRST_YEAR_TAX_SAVINGS=g.FIRST_YEAR_TAX_SAVINGS;this.DS_TERM15_PAYMENTS=g.DS_PAYMENTS;this.DS_TERM15_INTEREST_PAID=g.DS_INTEREST_PAID;this.DS_TERM15_PRINCIPAL_BAL=g.DS_PRINCIPAL_BAL;for(var b=this.TERM15_TERM+1;b<=this.TERM30_TERM;b++){this.DS_TERM15_PRINCIPAL_BAL[b]=0;this.DS_TERM15_PAYMENTS[b]=0;this.DS_TERM15_INTEREST_PAID[b]=0}g.clear();g.bWithSchedule=true;g.TERM=this.TERM30_TERM;g.INTEREST_RATE=a;g.MARGINAL_TAX_RATE=d;g.LOAN_AMOUNT=e;g.YEARS_IN_HOME=k;g.SAVINGS_RATE=h;g.BY_YEAR=f;g.calculate();this.TERM30_INTEREST_PAID=g.INTEREST_PAID;this.TERM30_TOTAL_OF_PAYMENTS=g.TOTAL_OF_PAYMENTS;this.TERM30_MONTHLY_PI=g.MONTHLY_PI;this.TERM30_FIRST_MONTH_INTEREST=g.FIRST_MONTH_INTEREST;this.TERM30_FIRST_MONTH_PRINCIPAL=g.FIRST_MONTH_PRINCIPAL;this.TERM30_AVG_TAX_SAVINGS=g.AVG_TAX_SAVINGS;this.TERM30_TAX_ADJ_RATE=g.TAX_ADJ_RATE;this.TERM30_FIRST_YEAR_INTEREST=g.FIRST_YEAR_INTEREST;this.TERM30_FIRST_YEAR_TAX_SAVINGS=g.FIRST_YEAR_TAX_SAVINGS;this.DS_TERM30_PRINCIPAL_BAL=g.DS_PRINCIPAL_BAL;this.DS_TERM30_PAYMENTS=g.DS_PAYMENTS;this.DS_TERM30_INTEREST_PAID=g.DS_INTEREST_PAID;this.PAYMENT_DIFFERENCE=this.TERM15_MONTHLY_PI-this.TERM30_MONTHLY_PI;this.INTEREST_DIFFERENCE=this.TERM30_INTEREST_PAID-this.TERM15_INTEREST_PAID;this.DS_TERM15_PAYMENT[0]=this.TERM15_MONTHLY_PI;this.DS_TERM30_PAYMENT[0]=this.TERM30_MONTHLY_PI;if(l){var m=this.sSchedule;m.clearRepeat();m.addHeader("&nbsp;",{sCell:KJE._sHeadingUnderline,sContent:m.sReportCol(c.number(this.TERM15_TERM)+" Mortgage Term",1),sFormat:"COLSPAN=3"},{sCell:KJE._sHeadingUnderline,sContent:m.sReportCol(c.number(this.TERM30_TERM)+" Mortgage Term",2),sFormat:"COLSPAN=3"});m.addHeader(m.sReportCol("Year",3),m.sReportCol("Payments",4),m.sReportCol("Interest",5),m.sReportCol("Balance",6),m.sReportCol("Payments",4),m.sReportCol("Interest",5),m.sReportCol("Balance",6));m.addRepeat("&nbsp;","&nbsp;","&nbsp;",c.dollars(this.DS_TERM15_PRINCIPAL_BAL[0],2),"&nbsp;","&nbsp;",c.dollars(this.DS_TERM30_PRINCIPAL_BAL[0],2));for(var b=0;b<this.TERM30_TERM;b++){if(b<this.TERM15_TERM){m.addRepeat(c.number(b+1),c.dollars(this.DS_TERM15_PAYMENTS[b+1],2),c.dollars(this.DS_TERM15_INTEREST_PAID[b+1],2),c.dollars(this.DS_TERM15_PRINCIPAL_BAL[b+1],2),c.dollars(this.DS_TERM30_PAYMENTS[b+1],2),c.dollars(this.DS_TERM30_INTEREST_PAID[b+1],2),c.dollars(this.DS_TERM30_PRINCIPAL_BAL[b+1],2))}else{m.addRepeat(c.number(b+1),c.dollars(0,2),c.dollars(0,2),c.dollars(0,2),c.dollars(this.DS_TERM30_PAYMENTS[b+1],2),c.dollars(this.DS_TERM30_INTEREST_PAID[b+1],2),c.dollars(this.DS_TERM30_PRINCIPAL_BAL[b+1],2))}}}};KJE.MortgageCompareCalc.prototype.formatReport=function(b){var c=KJE;var a=this.iDecimal;var d=b;d=KJE.replace("TERM15_INTEREST_RATE",c.percent(this.TERM15_INTEREST_RATE/100,3),d);d=KJE.replace("TERM30_INTEREST_RATE",c.percent(this.TERM30_INTEREST_RATE/100,3),d);d=KJE.replace("MARGINAL_TAX_RATE",c.percent(this.MARGINAL_TAX_RATE/100),d);d=KJE.replace("LOAN_AMOUNT",c.dollars(this.LOAN_AMOUNT),d);d=KJE.replace("YEARS_IN_HOME",c.number(this.YEARS_IN_HOME),d);d=KJE.replace("SAVINGS_RATE",c.percent(this.SAVINGS_RATE/100,2),d);d=KJE.replace("TERM15_INTEREST_PAID",c.dollars(this.TERM15_INTEREST_PAID),d);d=KJE.replace("TERM15_TOTAL_OF_PAYMENTS",c.dollars(this.TERM15_TOTAL_OF_PAYMENTS),d);d=KJE.replace("TERM15_MONTHLY_PI",c.dollars(this.TERM15_MONTHLY_PI,2),d);d=KJE.replace("TERM15_FIRST_MONTH_INTEREST",c.dollars(this.TERM15_FIRST_MONTH_INTEREST,2),d);d=KJE.replace("TERM15_FIRST_MONTH_PRINCIPAL",c.dollars(this.TERM15_FIRST_MONTH_PRINCIPAL,2),d);d=KJE.replace("TERM15_AVG_TAX_SAVINGS",c.dollars(this.TERM15_AVG_TAX_SAVINGS),d);d=KJE.replace("TERM15_TAX_ADJ_RATE",c.percent(this.TERM15_TAX_ADJ_RATE,2),d);d=KJE.replace("TERM15_FIRST_YEAR_INTEREST",c.dollars(this.TERM15_FIRST_YEAR_INTEREST),d);d=KJE.replace("TERM15_FIRST_YEAR_TAX_SAVINGS",c.dollars(this.TERM15_FIRST_YEAR_TAX_SAVINGS),d);d=KJE.replace("TERM30_INTEREST_PAID",c.dollars(this.TERM30_INTEREST_PAID),d);d=KJE.replace("TERM30_TOTAL_OF_PAYMENTS",c.dollars(this.TERM30_TOTAL_OF_PAYMENTS),d);d=KJE.replace("TERM30_MONTHLY_PI",c.dollars(this.TERM30_MONTHLY_PI,2),d);d=KJE.replace("TERM30_FIRST_MONTH_INTEREST",c.dollars(this.TERM30_FIRST_MONTH_INTEREST,2),d);d=KJE.replace("TERM30_FIRST_MONTH_PRINCIPAL",c.dollars(this.TERM30_FIRST_MONTH_PRINCIPAL,2),d);d=KJE.replace("TERM30_AVG_TAX_SAVINGS",c.dollars(this.TERM30_AVG_TAX_SAVINGS),d);d=KJE.replace("TERM30_TAX_ADJ_RATE",c.percent(this.TERM30_TAX_ADJ_RATE,2),d);d=KJE.replace("TERM30_FIRST_YEAR_INTEREST",c.dollars(this.TERM30_FIRST_YEAR_INTEREST),d);d=KJE.replace("TERM30_FIRST_YEAR_TAX_SAVINGS",c.dollars(this.TERM30_FIRST_YEAR_TAX_SAVINGS),d);d=KJE.replace("INTEREST_DIFFERENCE",c.dollars(this.INTEREST_DIFFERENCE),d);d=KJE.replace("PAYMENT_DIFFERENCE",c.dollars(this.PAYMENT_DIFFERENCE),d);d=d.replace("**REPEATING GROUP**",this.sSchedule.getRepeat());this.sSchedule.clearRepeat();return d};KJE.CalcName="Mortgage Comparison: 15 Years vs. 30 Years";KJE.CalcType="mortgagecompare";KJE.CalculatorTitleTemplate="15 year term saves you KJE1, but is KJE2 more per month";KJE.initialize=function(){KJE.CalcControl=new KJE.MortgageCompareCalc();KJE.GuiControl=new KJE.MortgageCompare(KJE.CalcControl)};KJE.MortgageCompare=function(j){var e=KJE;var b=KJE.gLegend;var f=KJE.inputs.items;this.MSG_MONTHLY_PAYMENTS=KJE.parameters.get("MSG_MONTHLY_PAYMENTS","Monthly Payments");this.MSG_PRINCIPAL_BALANCE=KJE.parameters.get("MSG_PRINCIPAL_BALANCE","Principal Balance by Year");this.MSG_YEAR_NUMBER=KJE.parameters.get("MSG_YEAR_NUMBER","Year Number");this.MSG_TERM15=KJE.parameters.get("MSG_TERM15","15 Year");this.MSG_TERM30=KJE.parameters.get("MSG_TERM30","30 Year");KJE.PercentSlider("MARGINAL_TAX_RATE","Your marginal tax rate",0,60,2);KJE.MortgageAmtSlider("LOAN_AMOUNT","Mortgage amount");KJE.InputItem.AltHelpName="INTEREST_RATE";KJE.MortgageRateSlider("TERM15_INTEREST_RATE","Interest rate for 15 years");KJE.InputItem.AltHelpName="INTEREST_RATE";KJE.MortgageRateSlider("TERM30_INTEREST_RATE","Interest rate for 30 years");KJE.InputItem.AltHelpName="MONTHLY_PI";KJE.Label("TERM30_MONTHLY_PI","30 year monthly payment",null,null,"bold");KJE.InputItem.AltHelpName="MONTHLY_PI";KJE.Label("TERM15_MONTHLY_PI","15 year monthly payment",null,null,"bold");var g=KJE.gNewGraph(KJE.gCOLUMN,"GRAPH1",true,false,KJE.colorList[1],this.MSG_MONTHLY_PAYMENTS);g._showItemLabelOnTop=true;g._showItemLabel=true;g._axisX._fSpacingPercent=0.5;g._grid._showYGridLines=false;g._legend._iOrientation=b.TOP_RIGHT;var h=KJE.gNewGraph(KJE.gLINE,"GRAPH2",true,true,KJE.colorList[1],this.MSG_PRINCIPAL_BALANCE);h._legend._iOrientation=b.TOP_RIGHT;h._titleXAxis.setText(this.MSG_YEAR_NUMBER);var a=KJE.parameters.get("MSG_DROPPER_TITLE","Mortgage Inputs");var c=KJE.parameters.get("MSG_DROPPER_CLOSETITLE","Loan amount of KJE1, 15 year interest rate KJE2, 30 year interest rate KJE3");var d=function(){return a+KJE.subText(KJE.getKJEReplaced(c,f.LOAN_AMOUNT.getFormatted(),f.TERM15_INTEREST_RATE.getFormatted(),f.TERM30_INTEREST_RATE.getFormatted()),"KJECenter")};KJE.addDropper(new KJE.Dropper("INPUTS",true,a,d),KJE.colorList[0])};KJE.MortgageCompare.prototype.setValues=function(b){var a=KJE.inputs.items;b.TERM15_INTEREST_RATE=a.TERM15_INTEREST_RATE.getValue();b.TERM30_INTEREST_RATE=a.TERM30_INTEREST_RATE.getValue();b.MARGINAL_TAX_RATE=a.MARGINAL_TAX_RATE.getValue();b.LOAN_AMOUNT=a.LOAN_AMOUNT.getValue();b.BY_YEAR=true};KJE.MortgageCompare.prototype.refresh=function(f){var e=KJE;var d=KJE.gLegend;var b=KJE.inputs.items;var a=KJE.gGraphs[0];var c=KJE.gGraphs[1];KJE.setTitleTemplate(e.dollars(f.INTEREST_DIFFERENCE),e.dollars(f.PAYMENT_DIFFERENCE));a.removeAll();a.setGraphCategories(f.payments);a.add(new KJE.gGraphDataSeries(f.DS_TERM15_PAYMENT,this.MSG_TERM15,a.getColor(1)));a.add(new KJE.gGraphDataSeries(f.DS_TERM30_PAYMENT,this.MSG_TERM30,a.getColor(2)));a.paint();c.removeAll();c.setGraphCategories(f.cats);c.add(new KJE.gGraphDataSeries(f.DS_TERM15_PRINCIPAL_BAL,this.MSG_TERM15,a.getColor(1)));c.add(new KJE.gGraphDataSeries(f.DS_TERM30_PRINCIPAL_BAL,this.MSG_TERM30,a.getColor(2)));c.paint();b.TERM15_MONTHLY_PI.setText(e.dollars(f.TERM15_MONTHLY_PI,2));b.TERM30_MONTHLY_PI.setText(e.dollars(f.TERM30_MONTHLY_PI,2))};KJE.InputScreenText=" <div id=KJE-D-INPUTS><div id=KJE-P-INPUTS>Input information:</div></div> <div id=KJE-E-INPUTS > <div id='KJE-C-LOAN_AMOUNT'><input id='KJE-LOAN_AMOUNT' /></div> <div id='KJE-C-MARGINAL_TAX_RATE'><input id='KJE-MARGINAL_TAX_RATE' /></div> <div id='KJE-C-TERM15_INTEREST_RATE'><input id='KJE-TERM15_INTEREST_RATE' /></div> <div id='KJE-C-TERM15_TOTAL_OF_PAYMENTS'><div id='KJE-TERM15_TOTAL_OF_PAYMENTS'></div></div> <div id='KJE-C-TERM30_INTEREST_RATE'><input id='KJE-TERM30_INTEREST_RATE' /></div> <div id='KJE-C-TERM30_TOTAL_OF_PAYMENTS'><div id='KJE-TERM30_TOTAL_OF_PAYMENTS'></div></div> <div style=\"height:10px\"></div> <div id='KJE-C-TERM15_MONTHLY_PI'><div id='KJE-TERM15_MONTHLY_PI'></div></div> <div id='KJE-C-TERM30_MONTHLY_PI'><div id='KJE-TERM30_MONTHLY_PI'></div></div> <div style=\"height:10px\"></div> </div> **GRAPH1** **GRAPH2** ";KJE.DefinitionText=" <div id='KJE-D-LOAN_AMOUNT' ><dt>Mortgage amount</dt><dd>Original balance of your mortgage.</dd></div> <div id='KJE-D-INTEREST_RATE' ><dt>Interest rate</dt><dd>Annual interest rate for your mortgage. Interest rates are generally lower for shorter term mortgages.</dd></div> <div id='KJE-D-MARGINAL_TAX_RATE' ><dt>Marginal tax rate</dt><dd>This is your combined state and federal tax rate. This is used to calculate possible income tax savings by deducting your mortgage interest. **TAXTABLE_CURRENT_DEFINITION** <div id='KJE-D-MONTHLY_PI' ><dt>Monthly payment</dt><dd>Monthly principal and interest payment (PI). Both 30 year fixed and 15 year fixed mortgages are shown.</dd></div> <div id='KJE-D-TOTAL_OF_PAYMENTS' ><dt>Total payments</dt><dd>Total of all monthly payments made over the full term of the mortgage. Both 30 year fixed and 15 year fixed mortgages are shown.</dd></div> <div id='KJE-D-INTEREST_PAID' ><dt>Total interest</dt><dd>Total of all interest paid over the full term of the mortgage. Both 30 year fixed and 15 year fixed mortgages are shown.</dd></div> <p>*Please consult with a tax professional regarding mortgage interest deductions and your specific situation. ";KJE.ReportText=' <!--HEADING "Mortgage Comparison: 15 Years vs. 30 Years" HEADING--> <h2 class=\'KJEReportHeader KJEFontHeading\'> Your monthly payment increases PAYMENT_DIFFERENCE with a 15 Year Fixed term, but you save INTEREST_DIFFERENCE in total interest paid over the life of the loan.</h2> A 15 year fixed mortgage term will save you INTEREST_DIFFERENCE in total interest paid, but increases your monthly payment by PAYMENT_DIFFERENCE. Total payments for a LOAN_AMOUNT, 15 year mortgage at TERM15_INTEREST_RATE are TERM15_TOTAL_OF_PAYMENTS. Total payments for the same loan with a 30 year mortgage at TERM30_INTEREST_RATE are TERM30_TOTAL_OF_PAYMENTS. **GRAPH** <div class=KJEReportTableDiv><table class=KJEReportTable><caption class=\'KJEHeaderRow KJEHeading\'>Mortgage Comparison</caption> <tr class=KJEFooterRow><td class="KJELabel KJECellBorder KJECell50">&nbsp;</td><td class="KJECellStrong KJECellBorder KJECell25" ALIGN=CENTER>15 year mortgage</td><td class="KJECellStrong KJECell25" ALIGN=CENTER>30 year mortgage</td></tr> <tr class=KJEOddRow><td class="KJELabel KJECellBorder">Loan amount</td><td class="KJECell KJECellBorder">LOAN_AMOUNT</td><td class="KJECell">LOAN_AMOUNT</td></tr> <tr class=KJEEvenRow><td class="KJELabel KJECellBorder">Interest rate</td><td class="KJECell KJECellBorder">TERM15_INTEREST_RATE</td><td class="KJECell">TERM30_INTEREST_RATE</td></tr> <tr class=KJEOddRow><td class="KJELabel KJECellBorder">Monthly payment</td><td class="KJECell KJECellBorder">TERM15_MONTHLY_PI</td><td class="KJECell">TERM30_MONTHLY_PI</td></tr> <tr class=KJEEvenRow><td class="KJELabel KJECellBorder">Total interest</td><td class="KJECell KJECellBorder">TERM15_INTEREST_PAID</td><td class="KJECell">TERM30_INTEREST_PAID</td></tr> <tr class=KJEOddRow><td class="KJELabel KJECellBorder">Total payments</td><td class="KJECell KJECellBorder">TERM15_TOTAL_OF_PAYMENTS</td><td class="KJECell">TERM30_TOTAL_OF_PAYMENTS</td></tr></table> </div> <h2 class=\'KJEReportHeader KJEFontHeading\'>Interest and Income Taxes*</h2> Changing your mortgage term can make a difference in not only the interest you pay, but also your income taxes. A longer mortgage term may increase your income tax deduction, as illustrated below. <div class=KJEReportTableDiv><table class=KJEReportTable><caption class=\'KJEHeaderRow KJEHeading\'>Interest and Income Tax Comparison</caption> <tr class=KJEFooterRow><td class="KJELabel KJECellBorder KJECell50">&nbsp;</td><td class="KJECellStrong KJECellBorder KJECell25" ALIGN=CENTER>15 year mortgage</td><td class="KJECellStrong KJECell25" ALIGN=CENTER>30 year mortgage</td></tr> <tr class=KJEOddRow><td class="KJELabel KJECellBorder">First month\'s interest</td><td class="KJECell KJECellBorder">TERM15_FIRST_MONTH_INTEREST </td><td class="KJECell">TERM30_FIRST_MONTH_INTEREST</td></tr> <tr class=KJEEvenRow><td class="KJELabel KJECellBorder">First month\'s principal</td><td class="KJECell KJECellBorder">TERM15_FIRST_MONTH_PRINCIPAL</td><td class="KJECell">TERM30_FIRST_MONTH_PRINCIPAL</td></tr> <tr class=KJEOddRow><td class="KJELabel KJECellBorder">First year\'s interest</td><td class="KJECell KJECellBorder">TERM15_FIRST_YEAR_INTEREST</td><td class="KJECell">TERM30_FIRST_YEAR_INTEREST</td></tr> <tr class=KJEEvenRow><td class="KJELabel KJECellBorder">First year\'s tax savings</td><td class="KJECell KJECellBorder">TERM15_FIRST_YEAR_TAX_SAVINGS</td><td class="KJECell">TERM30_FIRST_YEAR_TAX_SAVINGS</td></tr> <tr class=KJEOddRow><td class="KJELabel KJECellBorder">Avg. year\'s tax savings</td><td class="KJECell KJECellBorder">TERM15_AVG_TAX_SAVINGS</td><td class="KJECell">TERM30_AVG_TAX_SAVINGS</td></tr></table> </div> **GRAPH** <h2 class=\'KJEScheduleHeader KJEFontHeading\'>Payment Schedule</h2> **REPEATING GROUP** <p>*Please consult with a tax professional regarding mortgage interest deductions and your specific situation.</p> ';