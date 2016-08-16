<?php
/***********************************************************************/
// Arabic language pack
// contributed by: chatshia.com
/***********************************************************************/
/* Please submit corrections to feedback@osicodesinc.com - Thank you! */

// IMPORTANT: in the PHP Live! setup area where you create departments, you should also
// edit the "Chatting", "Offline Msg" and "Transcript Msg" to your language

$LANG = Array() ;
// do not attempt to modify the CHARSET unless the characters are not displaying properly
$LANG["CHARSET"] = "UTF-8" ;

/* visitor chat window */
$LANG["CHAT_WELCOME"] = "مرحبا بك في حوارنا الحي" ;
$LANG["CHAT_WELCOME_SUBTEXT"] = "ليسهل علينا خدمتك، نأمل منك تزويدنا بما يلي" ;
$LANG["CHAT_SELECT_DEPT"] = "--- إختر الإدارة ---" ;
$LANG["CHAT_BTN_START_CHAT"] = "إبداء الحوار" ;
$LANG["CHAT_BTN_EMAIL"] = "أرسل بريد" ;
$LANG["CHAT_BTN_EMAIL_TRANS"] = "أرسل نسخة من المحادثة" ;
$LANG["CHAT_PRINT"] = "اطبع المحادثة" ;
$LANG["CHAT_CHAT_WITH"] = "أنت تتحاور الآن مع" ;
$LANG["CHAT_SURVEY_THANK"] = "تم إرسال تقييمك للمحادثة، شكراً لك." ;
$LANG["CHAT_CLOSE"] = "أغلق المحادثة" ;
$LANG["CHAT_SOUND"] = "تبديل الصوت" ;
$LANG["CHAT_TRANSFER"] = "جاري تحويل المحادثة لـ" ;
$LANG["CHAT_TRANSFER_TIMEOUT"] = "تحويل المحادثات غير متوفر الآن.  جاري إعادة ربط المحادثة بالمأمور السابق..." ;


/* leave a message area */
$LANG["MSG_LEAVE_MESSAGE"] = "الرجاء أترك رسالة." ;
$LANG["MSG_EMAIL_FOOTER"] = "تم إرسال الرسالة عبر نظام المحادثة الحي للرسائل" ;
$LANG["MSG_PROCESSING"] = "جاري معالجة الرسالة السابق، الرجاء المحاولة لاحقاً في أقرب فرصة." ;


/* internal text */
$LANG["TRANSCRIPT_SUBJECT"] = "متن محادثتك السابقة مع" ;


/* chat notifications */
$LANG["CHAT_NOTIFY_JOINED"] = "إنضم إلى المحادثة الآن." ;
$LANG["CHAT_NOTIFY_RATE"] = "ماهو تقييمك للمحادثة التي كنت فيها الآن؟" ;
$LANG["CHAT_NOTIFY_DISCONNECT"] = "إنسحب العميل من المحادثة أو إنقطع الخط.  لقد تم إنهاء جلسة المحادثة." ;
$LANG["CHAT_NOTIFY_VDISCONNECT"] = "تم قطع الإتصال من طرف العميل.  لقد إنتهت جلسة المحادثة." ;
$LANG["CHAT_NOTIFY_ODISCONNECT"] = "تم قطع الإتصال من طرف المشغل .  إنتهت جلسة المحادثة." ;
$LANG["CHAT_NOTIFY_LOOKING_FOR_OP"] = "سيتم الرد على إتصالك من قبل ممثلين خلال ثواني، نشكر لكم إنتظاركم ونتطلع لخدمتكم." ;
$LANG["CHAT_NOTIFY_OP_NOT_FOUND"] = "جميع ممثلي خدمات العملاء غير متوفرين حالياً، نأمل منكم ترك رسالة وسنعاود الإتصال بكم لاحقاً، وشكراً لكم." ;
$LANG["CHAT_NOTIFY_IDLE_TITLE"] = "دردشة خاملا. يرجى ارسال الرد." ;
$LANG["CHAT_NOTIFY_IDLE_AUTO_DISCONNECT"] = "إغلاق تلقائيا دردشة" ;


/* javascript alerts */
$LANG["CHAT_JS_BLANK_DEPT"] = "الرجاء إختيار قسم." ;
$LANG["CHAT_JS_BLANK_NAME"] = "نأمل تزويدنا باسمك." ;
$LANG["CHAT_JS_BLANK_EMAIL"] = "نأمل تزويدنا ببريدك الإلكتروني." ;
$LANG["CHAT_JS_INVALID_EMAIL"] = "نسق البريد الإلكتروني غير صحيح.  (مثال: someone@somewhere.com)" ;
$LANG["CHAT_JS_BLANK_SUBJECT"] = "نأمل تزويدنا بموضوعك" ;
$LANG["CHAT_JS_BLANK_QUESTION"] = "نأمل تزويدنا بتساؤلك" ;
$LANG["CHAT_JS_LEAVE_MSG"] = "المحادثة الحية: أترك رسالة" ;
$LANG["CHAT_JS_EMAIL_SENT"] = "تم الإرسال" ;
$LANG["CHAT_JS_CHAT_EXIT"] = "شكراً لمحادثتك معنا، ونتطلع لخدمتك مجدداً" ;
$LANG["CHAT_JS_CUSTOM_BLANK"] = "نأمل تزويدنا بـ" ;


/* words */
$LANG["TXT_DEPARTMENT"] = "القسم" ;
$LANG["TXT_ONLINE"] = "متصل الآن" ;
$LANG["TXT_OFFLINE"] = "غير متصل" ;
$LANG["TXT_NAME"] = "الاسم" ;
$LANG["TXT_EMAIL"] = "البريد الإلكتروني" ;
$LANG["TXT_QUESTION"] = "السؤال" ;
$LANG["TXT_CONNECT"] = "إتصل" ;
$LANG["TXT_CONNECTING"] = "جاري الإتصال..." ;
$LANG["TXT_SUBMIT"] = "أرسل" ;
$LANG["TXT_DISCONNECT"] = "قطع الإتصال" ;
$LANG["TXT_SUBJECT"] = "الموضوع" ;
$LANG["TXT_MESSAGE"] = "الرسالة" ;
$LANG["TXT_LIVECHAT"] = "المحادثة الحية" ;
$LANG["TXT_OPTIONAL"] = "إختياري" ;
$LANG["TXT_TYPING"] = "يقوم بالكتابة الآن..." ;
$LANG["TXT_SECONDS"] = "ثواني" ;


/* as of v.4.5.9, all new lang vars will be included here in sequential order */
$LANG["CHAT_COMMENT_THANK"] = "التعليق حصل. شكرا." ;
$LANG["CHAT_JS_BLANK_COMMENT"] = "يرجى تقديم تعليق." ;
?>