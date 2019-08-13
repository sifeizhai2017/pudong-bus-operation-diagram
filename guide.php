
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <title>浦交模拟图</title>
    <style type="text/css">
        *
        {
            padding: 0;
            margin: 0;
            border: 0;
        }
        body
        {
            width: 100%;
            height: 100%;
            background: #ededf0;
            text-align: center;
            font-size: 16px;
            font-family: "微软雅黑";
            line-height: 1.5;
        }
        .queryBox
        {
            width: 100%;
            position: relative;
            margin-top: 10px;
            background: #fff;
            border-top: #c8c7cc 1px solid;
            border-bottom: #c8c7cc 1px solid;
        }
        input[name="bus"]
        {
            margin-left: 0.5em;
            display: block;
            width: 80%;
            height: 2.5em;
            font-size: 1.2em;
        }
        input[name="icon"]
        {
            position: absolute;
            z-index: 1;
            font-size: 1.2em;
            width: 2.5em;
            height: 2.5em;
            top: 0;
            right: 0;
            background: url(Images/bs.png) no-repeat center center;
            background-size: 50% 50%;
        }
        input:focus
        {
            outline: none;
        }
        .error
        {
            color: #f00;
        }
        .error, .taps
        {
            margin: 15px 0.5em 0;
            text-align: left;
        }
        .error
        {
            display: none;
        }
        .taps p
        {
            position: relative;
            text-align: left;
            font-size: 0.9em;
            color: #535353;
            padding-left: 20px;
            word-break: break-all;
        }
        .taps p span
        {
            position: absolute;
            z-index: 10;
            left: 0;
            top: 0;
        }
        
        ul#xc li
        {
            padding-left: 0.5em;
            display: block;
            height: 2.5em;
            font-size: 1.1em;
            line-height: 2.5em;
            border-bottom: #c8c7cc 1px solid;
        }
        ul#xc li:hover
        {
            background: #ccc;
        }
        ul#xc
        {
            width: 100%;
            background: #fff;
            z-index: 1000;
        }
        img.bottom
        {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            display: block;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="css/loading.css">
    <link href="css/loading.css" rel="stylesheet" type="text/css" />
    <script src="Scripts/jquery-1.4.1.min.js" type="text/javascript"></script>
    <script  type="text/javascript">
       function onBridgeReady(){
			WeixinJSBridge.call('hideOptionMenu');
		}
		
		$(function(){
		
		if (typeof WeixinJSBridge == "undefined"){
			if( document.addEventListener ){
				document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
			}else if (document.attachEvent){
				document.attachEvent('WeixinJSBridgeReady', onBridgeReady); 
				document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
			}
		}else{
			onBridgeReady();
		}
		
		});

		
        function showalert(param) {
            //alert("aaaaa");
            var key = param;
            var ih = $('#xc').html();
            var data = [
"浦东53路",
"1132路",
"浦东51路",
"测试路线001",
"浦东56路",
"167路",
"大泥专线",
"航大专线",
"455路",
"外高桥4路",
"453路",
"451路",
"990路",
"浦东19路",
"外高桥3路",
"1119路",
"沪川线",
"大桥五线",
"大桥四线",
"大桥六线",
"新芦专线",
"龙东专线",
"浦东33路",
"1096路",
"惠南1路",
"龙大专线",
"惠南3路",
"龙惠专线",
"惠南2路",
"惠临专线",
"惠芦专线",
"惠南4路",
"1127路",
"1128路",
"993路",
"浦东1路",
"871路",
"992路",
"陆家嘴金融城环线",
"989路",
"浦东28路",
"惠书专线",
"闵行20路",
"芦杜专线",
"1111路",
"1102路",
"1101路",
"浦东31路",
"浦东30路",
"986路区间",
"1129路",
"浦东18路",
"1094路",
"浦东24路",
"1136路",
"991路",
"636路",
"182路",
"浦东52路",
"1117路",
"浦东8路",
"浦东7路",
"1110路",
"1086路",
"1124路",
"1095路",
"1123路",
"798路",
"航奉专线",
"隧道一线",
"1089路",
"南新专线",
"1088路",
"1087路",
"1106路",
"1090路",
"1118路",
"浦东25路",
"周浦包车",
"1080路",
"1085路",
"1081路",
"1099路",
"1091路",
"1156路",
"隧道九线",
"1092路",
"龙港快线",
"1074路",
"1064路",
"隧道夜宵线",
"浦东12路",
"181路",
"1093路",
"1155路",
"周康9路",
"闵行39路",
"浦交交8",
"915路",
"隧道六线",
"浦东4路",
"浦东32路",
"1103路",
"1097路",
"通勤车4",
"浦东55路",
"轨交接驳122号线",
"716路",
"浦东54路",
"浦东58路",
"浦东12路区间",
"1049路",
"1048路",
"祝桥3路",
"638路",
"金祝专线",
"轨交接驳121号线",
"988路",
"轨交接驳62号线",
"640路",
"轨交接驳61号线",
"639路",
"1120路",
"1105路",
"1104路",
"843路",
"高东班车",
"一分定班",
"1100路",
"1121路",
"申崇二线区间",
"浦东35路",
"1010路",
"申崇六线",
"1031路",
"南园1路",
"219路",
"1051路",
"1053路",
"二分通勤车",
"1052路",

"984路",
"983路",
"机场七线",
"保税区5号",
"浦东50路区间",
"1055路",
"614路",
"986路",
"保税区4号",
"985路",
"陆家嘴金融城3路",
"130路",
"陆家嘴金融城2路",
"604路",
"987路",
"1083路",
"611路",
"610路",
"轨交接驳23号线",
"浦东47路",
"轨交接驳22号线",
"浦东48路",
"轨交接驳21号线",
"浦东26路",
"浦东27路",
"1058路",
"1056路",
"815路",
"1028路",
"1027路",
"1026路",
"1015路",
"1017路",
"1016路",
"申崇六线B",
"784路",
"浦东43路",
"786路",
"临港2路",
"775路",
"955路",
"779路",
"777路",
"780路",
"783路",
"南闵专线",
"782路",
"保税区1号",
"1047路",
"1042路",
"1045路",
"浦东20路",
"969路",
"1033路",
"保税区3号",
"保税区2号",
"169路",
"浦东40路",
"795路",
"981路",
"龙南定班线",
"573路",
"971路",
"787路",
"970路",
"576路",
"790路",
"454路",
"789路",
"浦东34路",
"119路",
"581路",
"792路",
"977路",
"791路",
"976路",
"临港4路",
"隧道三线",
"794路",
"980路",
"583路",
"978路",
"1134路",
"188路",
"浦东9路",
"沪南线",
"通勤车3",
"四分班车",
"642路",
"二分班车",
"浦东6路",
"三分班车",
"黄楼定班",
"通勤车1",
"浦东10路",
"联通班车",
"774路",
"交通车",
"343路",
"机场八线",
"浦东57路",
"778路",
"川沙2路",
"183路",
"申港1路",
"浦东37路",
"961区间",
"1071路",
"惠南11路",
"惠南6路",
"1069路",
"1068路",
"873路",
"1067路",
"1066路",
"洋山专线",
"1115路",
"1130路",
"1008路",
"1007路",
"浦东3路",
"自贸试验区洋山1线",
"航头3路",
"陆家嘴金融城1路",
"1003路",
"1002路",
"泥城2路",
"1001路",
"泥城1路",
"1059路",
"曹路2路",
"1133路",
"上南路救",
"338路",
"北蔡2路",
"339路",
"1009路",
"1006路",
"1005路",
"1004路",
"浦江3路",
"金高路救",
"曹路1路",
"浦江4路",
"三林1路",
"浦江6路",
"合庆1路",
"浦江5路",
"1073路",
"1072路",
"572路",
"浦东29路",
"1131路",
"1018路",
"1082路",
"1013路",
"1063路",
"周康10路",
"1078路",
"1024路",
"1077路",
"1023路",
"1075路",
"1019路",
"惠南5路",
"1079路",
"浦江7路",
"1020路",
"1012路",
"1035路",
"1011路",
"泥城5路",
"1135路",
"314路",
"外高桥1路",
"313路",
"金桥1路",
"1122路",
"1040路",
"1039路",
"陆家嘴金融城4路",
"六灶2路",
"1029路",
"1036路",
"申港3路",
"宣桥1路",
"1025路",
"北蔡1路",
"1022路",
"惠南8路",
"1021路",
"1070路",
"曹路4路",
"1038路",
"82路",
"81路",
"1046路",
"84路",
"1034路",
"83路",
"1065路",
"163路",
"1043路",
"85路",
"1041路",
"花木2路",
"1113路",
"785路",
"保税区6号",
"浦东38路",
"1076路",
"南川线",
"浦东41路",
"799路",
"浦东2路",
"浦东39路",
"川沙5路",
"新场5路",
"浦东22路",
"浦江11路",
"浦东45路",
"川航专线",
"浦东42路",
"申崇四线区间",
"浦江1路",
"芦潮港1路",
"申崇四线",
"祝桥2路",
"申崇二线",
"161路",
"书院2路",
"国际旅游度假区1路",
"浦江2路",
"1061路",
"1037路",
"国际旅游度假区2路",
"花木1路",
"书院3路",
"浦东50路",
"万祥2路",
"1126路",
"张江环线",
"177路",
"惠南10路",
"796路",
"175路",
"周南线",
"772路",
"张南专线",
"浦东23路",
"航头5路",
"新场1路",
"新场3路",
"新场2路",
"老港1路",
"祝桥1路",
"大团2路",
"995路",
"航头4路",
"曹路3路",
"周康6路",
"1158路",
"1157路",
"1050路",
"174路",
"浦东36路",
"川沙3路",
"961路",
"张江1路",
"南南线",
"周古定班车",
"康桥定班车",
"惠南5路（区间）",
"浦东46路",
"1112路",
"新川专线",
"1062路",
"泥城4路",
"隧道一线区间",

"1108路",
"周康1路",
"测试线路3",
"周康3路",
"周康2路",
"二分金科线",
"浦东72路",
"周康5路",
"洋山专线区间",
"周康4路",
"1109路",
"1054路",
"杨高包车",
"金桥包车一",
"浦东63路",
"浦东64路",
"浦东67路"

];
            if (key != '') {
                //这里给个全数据

                xt = check(data, key);
                //alert(xt);
                $('#xc').html('');
                $('#xc').append(xt);
                $('#xc').css('display', 'block');
            } else {
                $('#xc').html('');
            }
        }

        function check(data, key) {
            var out = '';
            var temp = new Array();
            for (var i = 0; i < data.length; i++) {
                var s = data[i].indexOf(key);
                if (s == 0 && data[i] != key) {
                    temp.push(data[i]);
                }
            }


            temp.sort(function (a, b) {
                var fa = a.replace(/[^0-9]/ig, "");
                var fb = b.replace(/[^0-9]/ig, "");

                var ta = parseInt(fa);
                var tb = parseInt(fb);

                return ta - tb;
            });
            for (var ii = 0; ii < temp.length; ii++) {
                out += "<li onClick=\"javascript:change('" + temp[ii] + "')\">" + temp[ii] + "</li>";
            }
            return out;
        }

        function change(val) {
            $('#bus').val(val);
            $('#xc').html('');
            $('#check').click();
        }
        $(function () {

            $('#check').click(function () {
                var line = $("#bus").val();
                if (line == "") {
                    $("#error").html("请选择线路");
                    $('#error').show();
                    return;
                }
                $("#mask,#loader").show();
                var urlx = "index.php?roadline=" + escape(line);
                location.href = urlx;

            });
        })

        function urlencode(str) {
            str = (str + '').toString();

            return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').
    replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
        }

        //===base64 start====
        var base64EncodeChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
        var base64DecodeChars = new Array(-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1, -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1);
        /**
        * base64编码
        * @param {Object} str
        */
        function base64encode(str) {
            var out, i, len;
            var c1, c2, c3;
            len = str.length;
            i = 0;
            out = "";
            while (i < len) {
                c1 = str.charCodeAt(i++) & 0xff;
                if (i == len) {
                    out += base64EncodeChars.charAt(c1 >> 2);
                    out += base64EncodeChars.charAt((c1 & 0x3) << 4);
                    out += "==";
                    break;
                }
                c2 = str.charCodeAt(i++);
                if (i == len) {
                    out += base64EncodeChars.charAt(c1 >> 2);
                    out += base64EncodeChars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
                    out += base64EncodeChars.charAt((c2 & 0xF) << 2);
                    out += "=";
                    break;
                }
                c3 = str.charCodeAt(i++);
                out += base64EncodeChars.charAt(c1 >> 2);
                out += base64EncodeChars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
                out += base64EncodeChars.charAt(((c2 & 0xF) << 2) | ((c3 & 0xC0) >> 6));
                out += base64EncodeChars.charAt(c3 & 0x3F);
            }
            return out;
        }
        /**
        * base64解码
        * @param {Object} str
        */
        function base64decode(str) {
            var c1, c2, c3, c4;
            var i, len, out;
            len = str.length;
            i = 0;
            out = "";
            while (i < len) {
                /* c1 */
                do {
                    c1 = base64DecodeChars[str.charCodeAt(i++) & 0xff];
                }
                while (i < len && c1 == -1);
                if (c1 == -1)
                    break;
                /* c2 */
                do {
                    c2 = base64DecodeChars[str.charCodeAt(i++) & 0xff];
                }
                while (i < len && c2 == -1);
                if (c2 == -1)
                    break;
                out += String.fromCharCode((c1 << 2) | ((c2 & 0x30) >> 4));
                /* c3 */
                do {
                    c3 = str.charCodeAt(i++) & 0xff;
                    if (c3 == 61)
                        return out;
                    c3 = base64DecodeChars[c3];
                }
                while (i < len && c3 == -1);
                if (c3 == -1)
                    break;
                out += String.fromCharCode(((c2 & 0XF) << 4) | ((c3 & 0x3C) >> 2));
                /* c4 */
                do {
                    c4 = str.charCodeAt(i++) & 0xff;
                    if (c4 == 61)
                        return out;
                    c4 = base64DecodeChars[c4];
                }
                while (i < len && c4 == -1);
                if (c4 == -1)
                    break;
                out += String.fromCharCode(((c3 & 0x03) << 6) | c4);
            }
            return out;
        }
        /**
        * utf16转utf8
        * @param {Object} str
        */
        function utf16to8(str) {
            var out, i, len, c;
            out = "";
            len = str.length;
            for (i = 0; i < len; i++) {
                c = str.charCodeAt(i);
                if ((c >= 0x0001) && (c <= 0x007F)) {
                    out += str.charAt(i);
                }
                else
                    if (c > 0x07FF) {
                        out += String.fromCharCode(0xE0 | ((c >> 12) & 0x0F));
                        out += String.fromCharCode(0x80 | ((c >> 6) & 0x3F));
                        out += String.fromCharCode(0x80 | ((c >> 0) & 0x3F));
                    }
                    else {
                        out += String.fromCharCode(0xC0 | ((c >> 6) & 0x1F));
                        out += String.fromCharCode(0x80 | ((c >> 0) & 0x3F));
                    }
            }
            return out;
        }
        /**
        * utf8转utf16
        * @param {Object} str
        */
        function utf8to16(str) {
            var out, i, len, c;
            var char2, char3;
            out = "";
            len = str.length;
            i = 0;
            while (i < len) {
                c = str.charCodeAt(i++);
                switch (c >> 4) {
                    case 0:
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                        // 0xxxxxxx
                        out += str.charAt(i - 1);
                        break;
                    case 12:
                    case 13:
                        // 110x xxxx 10xx xxxx
                        char2 = str.charCodeAt(i++);
                        out += String.fromCharCode(((c & 0x1F) << 6) | (char2 & 0x3F));
                        break;
                    case 14:
                        // 1110 xxxx10xx xxxx10xx xxxx
                        char2 = str.charCodeAt(i++);
                        char3 = str.charCodeAt(i++);
                        out += String.fromCharCode(((c & 0x0F) << 12) | ((char2 & 0x3F) << 6) | ((char3 & 0x3F) << 0));
                        break;
                }
            }
            return out;
        }
        //demo
        //function doit(){
        //    var f = document.f;
        //    f.output.value = base64encode(utf16to8(f.source.value));
        //    f.decode.value = utf8to16(base64decode(f.output.value));
        //}

        //===base64 end =====
    </script>

    <div style="display: none;"><textarea></head></textarea></head></div>

<body>

    <div>
        <article>
            <div class="queryBox">
                <input type="text" autocomplete="off" id="bus" placeholder="Please Type the line name here" value="" name="bus"
                    oninput="showalert(this.value)" />
                <input type="submit" id="check" value="" name="icon" />
            </div>
            <div style="text-align: left; position: relative; z-index: 100; width: 100%;">
                <ul id="xc">
                </ul>
            </div>
            <div class="error" id="error">
            </div>
            <div class="taps">
                <p>
                    <span>1、</span>请输入浦东公交的线路 如"576路"、"沪南线"、"外高桥3路"、"989路";</p>          
	        <p>
                    <span>1、</span>❤ Made By <a href="https://github.com/sjlleo/pudong-bus-operation-diagram">Leo</a></p> 
               <p>
            </div>
                        <div class="history">
        <div class="h-title">历史查询</div>
        <div class="h-items">
            <?php 
		session_start();
                $arr = array();
                $arr = $_SESSION['busp'];
                $arr = array_unique($arr);
                foreach ($arr as $a => $b) {
                    echo '<a href="./pudong/?roadline='.$b.'">'.$b.'</a> ';
                }
            ?>
                    </div>
        </article>
        <div id="mask">
        </div>
        <div id="loader">
            <div id="fountainG">
                <div id="fountainG_1" class="fountainG">
                </div>
                <div id="fountainG_2" class="fountainG">
                </div>
                <div id="fountainG_3" class="fountainG">
                </div>
                <div id="fountainG_4" class="fountainG">
                </div>
                <div id="fountainG_5" class="fountainG">
                </div>
                <div id="fountainG_6" class="fountainG">
                </div>
                <div id="fountainG_7" class="fountainG">
                </div>
                <div id="fountainG_8" class="fountainG">
                </div>
            </div>
        </div>
                            

    </div>
</body>
</html>
