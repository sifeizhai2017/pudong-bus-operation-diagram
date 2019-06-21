/// <reference path="jquery-1.4.1.min.js" />

var RoadLine = "926";
var getfirst = "";
var getlast = "";
var vn0 = ""; //定义当前上行发车
var vn1 = ""; //定义当前下行发车
var revid = ""; //传参的车辆编号
$(document).ready(
		function () {
		    var urlprm = window.location.search;
		    if (urlprm != "") {
		        var prms = urlprm.replace("?", "").split('&');
		        $(prms).each(function (i) {
		            var keyvalue = prms[i].split('=');
		            if (keyvalue[0] == "roadline") {
		                RoadLine = keyvalue[1];
		            }
		            else if (keyvalue[0] == "user") {
		                if (keyvalue[1] != "test") {
		                    window.location = "guide.htm";
		                }
		            }
		            if (keyvalue[0] == "vid") {
		                revid = keyvalue[1];
		            }
		        });
		        if (loadMould()) {
		            $("#divNull").hide();
		            Move();
		            setTimeout(TogetAll, 1000);
		            // setTimeout(Move, 3000);
		            setInterval(Move, 1000);
		        }
		        else {
		            $("#divNull").show();
		            $("#showModule").hide();
		            $("#loading").fadeOut();
		        }

		    }
		    else {
		        window.location = "guide.htm";
		    }

		});

var he1 = 0;
var he2 = 0;
var levelPosition1 = [];
var levelPosition2 = [];
//获取站点，显示
function loadMould() {
    var isfg = true; //定义返回变量
    var html1 = "";
    var html2 = "";
    var f1 = 0; //开往新开河
    var f2 = 0; //开往上海体育馆
    var url = "ajax/controller.php?Method=station&roadline=" + RoadLine;
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'text',
        async: false,
        timeout: 5000,
        error: function () {
            isfg = false;
        },
        success: function (data) {
            //  alert("aa");
            if (data == null || data == "" || data == undefined) {
                $("#RefreshM").show();
                $("#showModule").hide();
                $("#loading").fadeOut();
                isfg = false;
            }
            var data = $.parseJSON(data);
            var jdata = data.data;
            if (jdata.length > 0) {

                $("#RoadLine").html(jdata[0].RoadLine);
                for (var i = 0; i < jdata.length; i++) {
                    if (jdata[i].Upstream != "" && jdata[i].Upstream != null) {
                        if (f2 == 0) {
                            getfirst = jdata[i].ToDirection;
                        }
                        f2++;
                    } else {
                        if (f1 == 0) {
                            getlast = jdata[i].ToDirection;
                        }
                        f1++;
                    }
                }
                //计算站点的长度，均分
                he1 = parseInt((890 - (f1 - 2) * 13) / (f1 - 1));
                he2 = parseInt((890 - (f2 - 2) * 13) / (f2 - 1));
                var Anti = new Array();
                for (var i = 0; i < jdata.length; i++) {
                    if (jdata[i].Upstream != "" && jdata[i].Upstream != null) {
                        if (jdata[i].LevelId != "1") {

                            Anti.push(jdata[i].LevelName);
                        }
                        else {
                            html2 += '<div class="line" style="height:' + he2 + 'px"></div>';
                            $("#downst").html(jdata[i].LevelName);
                        }
                    }
                    else {

                        if (jdata[i].LevelId != "1" && jdata[i].LevelId != f1) {
                            html1 += '<div class="st"><div style="margin-left:-180px; width:175px;  text-align:right  ">' + jdata[i].LevelName + '</div></div>';
                            html1 += '<div class="line" style="height:' + he1 + 'px"></div>';
                        }
                        else {

                            if (jdata[i].LevelId == "1") {
                                html1 += '<div class="line" style="height:' + he1 + 'px"></div>';
                                $("#upst").html(jdata[i].LevelName);
                            }
                        }
                    }
                }
                for (var j = Anti.length - 1; j >= 0; j--) {
                    if (j != Anti.length - 1) {
                        html2 += '<div class="st"><div style="margin-left:20px; width:175px;  text-align:left ">' + Anti[j] + '</div></div>';
                        html2 += '<div class="line" style="height:' + he2 + 'px"></div>';
                    }

                }
                if (html1 != "" && html2 != "") {
                    $("#up").html(html1);
                    $("#down").html(html2);
                    isfg = true;
                    $("#showModule").show();
                    $("#loading").fadeOut();
                }
                else {
                    $("#RefreshM").show();
                    $("#showModule").hide();
                    isfg = false;
                }


            }
            else {

                isfg = false;
            }
        }
    });
    return isfg;
}


//添加集合存储车辆数据
var vlist = {};


//获取所有车，存放
function TogetAll() {
    var busCount = 0; //配车数
    var html1 = "";
    var html2 = "";
    var url = "ajax/controller.php?Method=gpsdata&roadline=" + RoadLine;
    $.get(url, function (data) {

        if (data == null || data == "" || data == undefined) {
            $("#RefreshM").show();
            $("#showModule").hide();
            return false;
        }
        var data = $.parseJSON(data);

        var jdata = data.data;
        if (jdata.length > 0) {
            for (var i = 0; i < jdata.length; i++) {
                var adurl = "http://116.236.170.106:9001/MobileWeb/ShowData.aspx?which=ad&roadline=&registrationmark=&VehicleNumbering=&AdContent=" + jdata[i].Adcode;
                //  html1 += '<div style="width:200px">';
                html1 += '<div  id="' + jdata[i].vnumber + '"  class="dnone" > <div class="tit" onclick="showContent(\'' + jdata[i].vnumber + '\')" >' + jdata[i].vnumber + '</div>  <div class="stit" onclick="showContent(\'' + jdata[i].vnumber + '\')" ></div></div>';
                html1 += ' <div  id="d' + jdata[i].vnumber + '" class="dnone" style=" width:220px; border:1px solid black;  background-color:white;  z-index:10000; height:110px;  ">';
                html1 += '<table border="0" style=" width:100%;text-align: left;"  >';
                html1 += '                 <tr><td></td><td></td></tr>';
                html1 += '                 <tr><td style=" width:40%">自编号</td><td>' + jdata[i].vnumber + '[' + jdata[i].vid + ']</td></tr>';
                html1 += '                 <tr><td>车速</td><td>' + jdata[i].Speed + 'km/h</td></tr>';
                html1 += '                  <tr><td>下一站位置</td><td>' + jdata[i].wz + '</td></tr>';
//                html1 += '                 <tr><td>营运状态</td><td>' + jdata[i].RunStatus + '</td></tr>';
                html1 += '                 <tr><td>定位时间</td><td>' + jdata[i].DWTime + '</td></tr>';
//                html1 += '                 <tr><td>广告品牌</td><td><a href="' + adurl + '">' + jdata[i].AdContent + '</a></td></tr>';
//                html1 += '                 <tr><td>司机名称</td><td>' + jdata[i].drivername + '</td></tr>';
//                html1 += '                 <tr><td>下刊时间</td><td>' + jdata[i].InsureEndDate + '</td></tr>';
                html1 += '           </table>';
                html1 += '       </div>';
                // html1 += '</div>';
            }

            //if (data.BusCount > 0) {
            //   $("#busCount").text(data.BusCount);
            busCount = data.busCount;
            $("#park").html(html1);
            //    return true;

            //}
            //else {
            //    $("#RefreshM").show();
            //    $("#showModule").hide();
            //    return false;
            //}
        }
        else {
            //TogetAll();
        }
        if (busCount < 2) {
            //  TogetAll();
        }


    });
}

var hisid = "";
function showContent(id) {
    //for (var i in vlist) {
    //    //if (man.hasOwnProperty(i)) { //filter,只输出man的私有属性
    //    //    console.log(i, ":", man[i]);
    //    //};
    //    $("#d" + i).addClass("dnone");
    //    //alert(i);

    //}

    if (hisid != id) {
        $("#d" + id).removeClass("dnone");
        $("#d" + hisid).addClass("dnone");
        $("#dd" + hisid1).addClass("dnone");
        hisid = id;
    }
    else {
        $("#d" + id).addClass("dnone");
        hisid = "";
    }
    //  alert(id);
    // $("#d" + id).removeClass("dnone");
}
var hisid1 = "";
function showContent1(id1) {

    if (hisid1 != id1) {
        $("#dd" + id1).removeClass("dnone");
        $("#dd" + hisid1).addClass("dnone");
        $("#d" + hisid).addClass("dnone");
        hisid1 = id1;
    }
    else {
        $("#dd" + id1).addClass("dnone");
        hisid1 = "";
    }
    //  alert(id);
    // $("#d" + id).removeClass("dnone");
}

/* Mark 1 的原理：
判断点击事件发生在区域外的条600030件是：
1. 点击事件的对象不是目标区域本身
2. 事件对象同时也不是目标区域的子元素
*/
$(document).mouseup(function (e) {
    var _con = $('body');   // 设置目标区域
    if (!_con.is(e.target) && _con.has(e.target).length <= 1) { // Mark 1
        $("#dd" + hisid1).addClass("dnone");
        $("#d" + hisid).addClass("dnone");
        hisid = "";
    }
});
//function hideCont() {

//    var _con = $('body');   // 设置目标区域
//    if (!_con.is(e.target) && _con.has(e.target).length === 0) { // Mark 1
//        $("#dd" + hisid1).addClass("dnone");
//        $("#d" + hisid).addClass("dnone");
//    }


//}



//展示车辆处于什么位置
var mg = 0;
var isFirst = false;
function Move() {
    //getStartScreen();
    if (isFirst) {
        if ((new Date().getSeconds()) % 5 != 0) {
            //console.log("秒：" + new Date().getSeconds());
            return;
        }
    }
    isFirst = true;
    getScreen();

    var LineCount = 0; //发车数
    var zdtb = 13;
    var html1 = "";
    var html2 = "";
    var Busrun = 0;
    var lastup = "";
    var lastdown = "";

    var url = "ajax/controller.php?Method=gpsdata&roadline=" + RoadLine;
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'text',
        cache: false,
        //      async: false,
        timeout: 25000,
        error: function () {
            //alert("服务器超时，请稍后再试！");
        },
        success: function (data) {
            //alert(data);
            //  alert("aa");
            vlist = {};
            var topNum = 0;
            //  var updown = "upInSt";
            if (data == null || data == "" || data == undefined) {
                $("#RefreshM").show();
                $("#showModule").hide();
                return false;
            }
            var data = $.parseJSON(data);
            var jdata = data.data;

            if (jdata.length > 0) {
                for (var i = 0; i < jdata.length; i++) {
                    Busrun = 0;
                    var adurl = "http://116.236.170.106:9001/MobileWeb/ShowData.aspx?which=ad&roadline=&registrationmark=&VehicleNumbering=&AdContent=" + jdata[i].Adcode;
                    if (jdata[i].state == "营运车辆") {
                        LineCount++;

                        if (jdata[i].todir == "0") {
                            if (parseInt(jdata[i].nextlevel) - 1 > 0) {
                                Busrun = he1 * (parseInt(jdata[i].nextlevel) - 2) + zdtb * (parseInt(jdata[i].nextlevel) - 2) + parseInt(he1 * jdata[i].rate);
                            }
                            else {
                                Busrun = parseInt(he1 * jdata[i].rate);
                            }
                            if (parseInt(jdata[i].stationid) > -1) {
                                if (parseInt(jdata[i].stationid) == 2) {
                                    Busrun = he1;
                                }
                                else {
                                    Busrun = he1 * (parseInt(jdata[i].stationid) - 1) + zdtb * (parseInt(jdata[i].stationid) - 2);
                                }
                                $("#" + jdata[i].vnumber).removeClass("dnone").removeClass("point1").addClass("upInSt").css("margin-top", (Busrun) + "px");
                                $("#" + jdata[i].vnumber).find(".tit").addClass("leftst").html(jdata[i].vnumber);
                                //$("#" + jdata[i].vnumber).find(".stit").addClass("leftst").html(jdata[i].vid);


                                //  $("#d" + jdata[i].vnumber).addClass("leftdi");
                                $("#d" + jdata[i].vnumber).addClass("leftdi").css("margin-top", Busrun + "px");

                            }
                            else {
                                Busrun += parseInt(he1 * jdata[i].rate)
                                $("#" + jdata[i].vnumber).removeClass("dnone").removeClass("upInSt").addClass("point1").css("margin-top", Busrun + "px");
                                $("#" + jdata[i].vnumber).find(".tit").addClass("leftst").html(jdata[i].vnumber );
                              //  $("#" + jdata[i].vnumber).find(".stit").addClass("leftst").html(jdata[i].vid);
                                $("#d" + jdata[i].vnumber).addClass("leftdi").css("margin-top", Busrun + "px");
                            }

                            //  vlist[jdata[i].vnumber] = Busrun;
                            topNum = Busrun;

                        }
                        else {
                            if (parseInt(jdata[i].nextlevel) - 1 > 0) {

                                //Busrun = he2 * (parseInt(jdata[i].nextlevel) - 2) + zdtb * (parseInt(jdata[i].nextlevel) - 2) + parseInt(he2 * jdata[i].rate);
                                //  console.log(($('#down .st').length - parseInt(jdata[i].nextlevel) + 1));
                                var nextlevel = parseInt(jdata[i].nextlevel);
                                if (nextlevel >= 2 && nextlevel < $('#down .st').length + 2) {
                                    Busrun = $('#down .st:eq(' + ($('#down .st').length - parseInt(jdata[i].nextlevel) + 1) + ')').offset().top - $('#down').offset().top + he2;
                                }
                            }
                            else {
                                Busrun = parseInt(he2 * jdata[i].rate);

                            }
                            if (parseInt(jdata[i].stationid) > -1) {
                                if (parseInt(jdata[i].stationid) == 2) {
                                    Busrun = he2;
                                }
                                else {
                                    Busrun = he2 * (parseInt(jdata[i].nextlevel) - 1) + zdtb * (parseInt(jdata[i].stationid));
                                }
                                $("#" + jdata[i].vnumber).removeClass("dnone").removeClass("point2").addClass("downInSt").css("margin-top", (/*890 -*/Busrun) + "px");
                                $("#" + jdata[i].vnumber).find(".tit").addClass("rightst").html(jdata[i].vnumber );
                                $("#" + jdata[i].vnumber).find(".stit").addClass("rightst").html(jdata[i].vid);
                                //   $("#d" + jdata[i].vnumber).addClass("rightdi");
                                $("#d" + jdata[i].vnumber).addClass("rightdi").css("margin-top", (/*890 -*/Busrun) + "px");
                            }
                            else {
                                Busrun += parseInt(he2 * jdata[i].rate);
                                $("#" + jdata[i].vnumber).removeClass("dnone").removeClass("downInSt").addClass("point2").css("margin-top", (/*890 -*/Busrun) + "px");
                                $("#" + jdata[i].vnumber).find(".tit").addClass("rightst").html(jdata[i].vnumber );
                         //       $("#" + jdata[i].vnumber).find(".stit").addClass("rightst").html(jdata[i].vid);
                                $("#d" + jdata[i].vnumber).addClass("rightdi").css("margin-top", (/*890 -*/Busrun) + "px");

                            }
                            topNum = /* 890 -*/Busrun;
                        }
                        //添加属性
                        $("#d" + jdata[i].vnumber + " table").find("tr:eq(2)").find("td:eq(1)").text(jdata[i].Speed + "km/h");
                        $("#d" + jdata[i].vnumber + " table").find("tr:eq(3)").find("td:eq(1)").text(jdata[i].LevelName);
                       // $("#d" + jdata[i].vnumber + " table").find("tr:eq(4)").find("td:eq(1)").text(jdata[i].RunStatus);
                        $("#d" + jdata[i].vnumber + " table").find("tr:eq(4)").find("td:eq(1)").text(jdata[i].DWTime);
                        $("#d" + jdata[i].vnumber + " table").find("tr:eq(5)").find("td:eq(1)").text(jdata[i].drivername);
                        //   $("tb" + jdata[i].vnumber).find("tr:eq(1)").find("td:lt(1):gt(1)").val(jdata[i].Speed);
                        //   alert($("#d" + jdata[i].vnumber + " table").find("tr:eq(3)").find("td:eq(1)").val())
                        //   break;
                        vlist[jdata[i].vnumber] = Busrun;
                        var ylr = parseInt($("#" + jdata[i].vnumber).css('left'));
                        if (isNaN(ylr))
                            ylr = parseInt($("#" + jdata[i].vnumber).css('right'));
                        //alert(ylr);
                        var mt = 0;
                        for (var m in vlist) {
                            //if (man.hasOwnProperty(i)) { //filter,只输出man的私有属性
                            //    console.log(i, ":", man[i]);
                            //};
                            //if (jdata[i].vnumber == m)
                            //    continue;
                            $("#d" + m).addClass("dnone");

                            var lr = parseInt($("#" + m).css('left'));
                            if (isNaN(lr))
                                lr = parseInt($("#" + m).css('right'));
                            var num = parseInt($("#" + m).css('marginTop'));

                            if (num <= (topNum + 10) && num >= (topNum - 10) && ylr == lr && !isNaN(lr)) {

                                $("#" + m).find(".tit").css("margin-top", (mt));
                                $("#d" + m).css("margin-top", (topNum + mt));
                                // alert(m);
                                mt += 10;
                                // break;
                                // setTimeout(1000);
                            }

                            //alert(i);

                        }
                    }
                    else if (jdata[i].state == getfirst) {
                        $("#" + jdata[i].vnumber).addClass("dnone");
                        $("#" + jdata[i].vnumber).removeClass("point2");
                        $("#" + jdata[i].vnumber).find(".tit").removeClass("rightst");
                        if (lastup == "") {
                            lastup += (jdata[i].vnumber == vn0 ? "<span class='redcolor' onclick='showContent1(\"" + jdata[i].vnumber + "\")'  >" + jdata[i].vnumber + "</span>" : "<span   onclick='showContent1(\"" + jdata[i].vnumber + "\")'>" + jdata[i].vnumber + "</span>");
                            lastup += ' <div  id="dd' + jdata[i].vnumber + '"  class="dnone" style=" width:220px; border:1px solid black;  background-color:white;  z-index:1000; height:110px; position: absolute;   ">';
                            lastup += '<table border="0" style=" width:100%;text-align: left;">';
                            lastup += '                 <tr><td></td><td></td></tr>';
                            lastup += '              <tr><td style=" width:40%">自编号</td><td>' + jdata[i].vnumber + '[' + jdata[i].vid + ']</td></tr>';
                            lastup += '                 <tr><td>车速</td><td>' + jdata[i].Speed + 'km/h</td></tr>';
                            lastup += '                  <tr><td>下一站位置</td><td>' + jdata[i].LevelName + '</td></tr>';
//                            lastup += '                 <tr><td>营运状态</td><td>' + jdata[i].RunStatus + '</td></tr>';
                            lastup += '                 <tr><td>定位时间</td><td>' + jdata[i].DWTime + '</td></tr>';
//                            lastup += '                 <tr><td>广告品牌</td><td><a href="' + adurl + '">' + jdata[i].AdContent + '</a></td></tr>';
//                            lastup += '                 <tr><td>司机名称</td><td>' + jdata[i].drivername + '</td></tr>';
//                            lastup += '                 <tr><td>下刊时间</td><td>' + jdata[i].InsureEndDate + '</td></tr>';
                            lastup += '           </table>';
                            lastup += '       </div>';
                        } else {
                            lastup += "," + (jdata[i].vnumber == vn0 ? "<span class='redcolor'   onclick='showContent1(\"" + jdata[i].vnumber + "\")'>" + jdata[i].vnumber + "</span>" : "<span   onclick='showContent1(\"" + jdata[i].vnumber + "\")'>" + jdata[i].vnumber + "</span>");
                            lastup += ' <div  id="dd' + jdata[i].vnumber + '"  class="dnone" style=" width:220px; border:1px solid black;  background-color:white;  z-index:1000; height:110px; position: absolute; ">';
                            lastup += '<table border="0" style=" width:100%;text-align: left;">';
                            lastup += '                 <tr><td></td><td></td></tr>';
                            lastup += '              <tr><td style=" width:40%">自编号</td><td>' + jdata[i].vnumber + '[' + jdata[i].vid + ']</td></tr>';
                            lastup += '                 <tr><td>车速</td><td>' + jdata[i].Speed + 'km/h</td></tr>';
                            lastup += '                  <tr><td>下一站位置</td><td>' + jdata[i].wz + '</td></tr>';
//                            lastup += '                 <tr><td>营运状态</td><td>' + jdata[i].RunStatus + '</td></tr>';
                            lastup += '                 <tr><td>定位时间</td><td>' + jdata[i].DWTime + '</td></tr>';
//                            lastup += '                 <tr><td>广告品牌</td><td><a href="' + adurl + '">' + jdata[i].AdContent + '</a></td></tr>';
//                            lastup += '                 <tr><td>司机名称</td><td>' + jdata[i].drivername + '</td></tr>';
//                            lastup += '                 <tr><td>下刊时间</td><td>' + jdata[i].InsureEndDate + '</td></tr>';
                            lastup += '           </table>';
                            lastup += '       </div>';
                        }
                        LineCount++;
                    }
                    else if (jdata[i].state == getlast) {
                        $("#" + jdata[i].vnumber).addClass("dnone");
                        $("#" + jdata[i].vnumber).removeClass("point1");
                        $("#" + jdata[i].vnumber).find(".tit").removeClass("leftst");
                        if (lastdown == "") {
                            lastdown += (jdata[i].vnumber == vn1 ? "<span class='redcolor'  onclick='showContent1(\"" + jdata[i].vnumber + "\")'>" + jdata[i].vnumber + "</span>" : "<span  onclick='showContent1(\"" + jdata[i].vnumber + "\")'>" + jdata[i].vnumber + "</span>");
                            lastdown += ' <div  id="dd' + jdata[i].vnumber + '"  class="dnone"   style=" width:220px; border:1px solid black;  background-color:white;  z-index:1000; height:110px;position: absolute;  top:-150px  ">';
                            lastdown += '<table border="0" style=" width:100%;text-align: left;">';
                            lastdown += '                  <tr><td></td><td></td></tr>';
                            lastdown += '              <tr><td style=" width:40%">自编号</td><td>' + jdata[i].vnumber + '[' + jdata[i].vid + ']</td></tr>';
                            lastdown += '                 <tr><td>车速</td><td>' + jdata[i].Speed + 'km/h</td></tr>';
                            lastdown += '                  <tr><td>下一站位置</td><td>' + jdata[i].wz + '</td></tr>';
//                            lastdown += '                 <tr><td>营运状态</td><td>' + jdata[i].RunStatus + '</td></tr>';
                            lastdown += '                 <tr><td>定位时间</td><td>' + jdata[i].DWTime + '</td></tr>';
//                            lastdown += '                 <tr><td>广告品牌</td><td><a href="' + adurl + '">' + jdata[i].AdContent + '</a></td></tr>';
//                            lastdown += '                 <tr><td>司机名称</td><td>' + jdata[i].drivername + '</td></tr>';
//                            lastdown += '                 <tr><td>下刊时间</td><td>' + jdata[i].InsureEndDate + '</td></tr>';
                            lastdown += '           </table>';
                            lastdown += '       </div>';
                        } else {
                            lastdown += "," + (jdata[i].vnumber == vn1 ? "<span class='redcolor'  onclick='showContent1(\"" + jdata[i].vnumber + "\")'>" + jdata[i].vnumber + "</span>" : "<span  onclick='showContent1(\"" + jdata[i].vnumber + "\")'>" + jdata[i].vnumber + "</span>");
                            lastdown += ' <div  id="dd' + jdata[i].vnumber + '" class="dnone"   style=" width:220px; border:1px solid black;  background-color:white;  z-index:1000; height:110px;  position: absolute; top:-150px " >';
                            lastdown += '<table border="0" style=" width:100%;text-align: left;">';
                            lastdown += '                  <tr><td></td><td></td></tr>';
                            lastdown += '              <tr><td style=" width:40%">自编号</td><td>' + jdata[i].vnumber + '[' + jdata[i].vid + ']</td></tr>';

                            lastdown += '                 <tr><td>车速</td><td>' + jdata[i].Speed + 'km/h</td></tr>';
                            lastdown += '                  <tr><td>下一站位置</td><td>' + jdata[i].wz + '</td></tr>';
                            lastdown += '                 <tr><td>营运状态</td><td>' + jdata[i].RunStatus + '</td></tr>';
                            lastdown += '                 <tr><td>定位时间</td><td>' + jdata[i].DWTime + '</td></tr>';
//                            lastdown += '                 <tr><td>广告品牌</td><td><a href="' + adurl + '">' + jdata[i].AdContent + '</a></td></tr>';
                            lastdown += '                 <tr><td>司机名称</td><td>' + jdata[i].drivername + '</td></tr>';
//                            lastdown += '                 <tr><td>下刊时间</td><td>' + jdata[i].InsureEndDate + '</td></tr>';
                            lastdown += '           </table>';
                            lastdown += '       </div>';
                        }
                        LineCount++;
                    }
                    else if (jdata[i].state == "报站") {
                        $("#" + jdata[i].vnumber).removeClass("dnone");
                    }
                    else {
                        $("#" + jdata[i].vnumber).addClass("dnone");
                        $("#" + jdata[i].vnumber).removeClass("point1");
                        $("#" + jdata[i].vnumber).find(".tit").removeClass("leftst");
                        $("#" + jdata[i].vnumber).removeClass("point2");
                        $("#" + jdata[i].vnumber).find(".tit").removeClass("rightst");
                    }
                    //判断是否有车牌号
                    if (revid == jdata[i].vid) {
                        $("#" + jdata[i].vnumber).css("color", "aqua");
                    }
                }
                //  $("#lineCount").text(LineCount);
                // getWorkBus();//当前营运车辆
                $("#lastup").html(lastup);
                $("#lastdown").html(lastdown);

                $("#showModule").show();
                if (hisid != "") {
                    $("#d" + hisid).removeClass("dnone");
                }
                $("#loading").fadeOut();
            }
            else {
                //  Move(); 
            }
        }
    });
}


function check(v) {

}

function getMinVa(f) {
    while (f % 2 > 0 || f % 3 > 0 || f % 5 > 0) {

        if (f % 2 > 0) {
            f = f / 2;
        }
        else if (f % 3 > 0) {
            f = f / 3;
        }
        else if (f % 5 > 0) {
            f = f / 5;
        }
        else {
            break;
        }

    }
    return f;
}

//得到发车屏信息 by sjlleo -2019-03-26
function getScreen() {
    var url = "ajax/controller.php?roadline=" + RoadLine + "&Method=departscreen&startstation=all";
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'text',
        cache: false,
        //  async: false,
        timeout: 5000,
        error: function () {
            return;
        },
        success: function (data) {
            var data = $.parseJSON(data);
            if (data.jhpc != undefined) {
                $('#busCount').text(data.jhpc);
            }
            if (data.dqyy != undefined) {
                $('#lineCount').text(data.dqyy);
            }
            var jdata = data.data;
            if (jdata.length > 0) {
                for (var i = 0; i < jdata.length; i++) {
                    if (jdata[i].dir == "0") {
                        $("#plan1").html(parseInt(jdata[i].jhjg));
                        $("#dplan1").html(parseInt(jdata[i].yjjg));
                        vn0 = jdata[0].VEHICLENUMBERING;
                        $("#vnup1").html(vn0);
                        // $("#" + vn1).css("color", "red");
                        $("#timeup1").html(jdata[i].PLANTIME);
                    }
                    else {
                        $("#plan2").html(parseInt(jdata[i].jhjg));
                        $("#dplan2").html(parseInt(jdata[i].yjjg));
                        vn1 = jdata[i].VEHICLENUMBERING;
                        $("#vndown1").html(vn1);
                        //   $("#" + vn0).addClass("redcolor");
                        $("#timedown1").html(jdata[i].PLANTIME);
                    }
                }


            }
        }
    });
}

//得到发车屏信息
function getStartScreen() {
    var busCount = 0; //配车数
    var html1 = "";
    var html2 = "";
    var url = "interface/Handler.ashx?action=departscreen&userid=test&password=test&roadline=" + RoadLine + "&startstation=all&format=xml";
    $.get(url, function (data) {
        $(data).find('result').each(function (i) {
            var $xml = $(this);
            //  var aa = $xml.find('vnumber').text();
            if ($xml.attr("dir") == "1") {
                $("#plan1").html($xml.find('jhjg').text());
                $("#dplan1").html($xml.find('yjjg').text());
                vn0 = $xml.find('Current').find('vnumber').text();
                $("#vnup1").html(vn0);
                // $("#" + vn1).css("color", "red");
                $("#timeup1").html($xml.find('Current').find('depart').text());
            }
            else if ($xml.attr("dir") == "0") {
                $("#plan2").html($xml.find('jhjg').text());
                $("#dplan2").html($xml.find('yjjg').text());
                vn1 = $xml.find('Current').find('vnumber').text();
                $("#vndown1").html(vn1);
                //   $("#" + vn0).addClass("redcolor");
                $("#timedown1").html($xml.find('Current').find('depart').text());

            }

        });



    });
}

//获取营运数据
function getWorkBus() {
    //var workCount = 0; //营运车数
    var url = "Ajax/Handler.ashx?Method=workBus&roadline=" + RoadLine;
    $.get(url, function (data) {
        $("#WorkCount").html(data);
    });
}
