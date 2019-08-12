<?php


/*
    2019-06-17 浦交模拟图V2 Beta
    Made by Leo
*/


/* 主控制模块
   gpsdata 获取gps等车辆到站数据
   station 获取站点信息数据
   departscreen 获取发车时间数据
*/
function check($roadline,$function)
{
    $line_info = get_line_id_v2($roadline);
    $line_id = $line_info['lineCode'];
    //系统中的线路ID
    $line_start = $line_info['startStationName'];
    //线路的起始站
    $line_end = $line_info['endStationName'];
    //线路的终点站
    
    switch ($function){
        case 'gpsdata':
            $station_data = station_output_A($roadline,stop_info($roadline,$line_id));
            print_r(json_encode((gps_data_get($roadline,$line_start,$line_end,$line_id,json_decode($station_data,true)))));
           break;
           
        case 'station':

            $station_data = station_output_A($roadline,stop_info($roadline,$line_id));
            print_r(($station_data));
            break;
        case 'departscreen':

            $depart_time = departscreen($line_id,$roadline);
            print_r($depart_time);
            break;
   }
   
    
}


/*
    实现离线读取line_id信息
    传入线路名即可得到线路id
*/

function get_line_id_v2($roadline)
{
        $file = "./lines.json";
        $data = file_get_contents($file); //读取缓存
        $data = json_decode($data,true);
        foreach ($data as $k => $v)
        {
            if ($v['name'] == $roadline) return $v;
            //根据线路名称查找线路id，找到返回id
        }
    
}

/* 
   模块A：对上游API的数据获取
   实现：Curl
   相关参数：
   1.URL为请求的API接口地址
   2.post_data为POST的数组存放
*/

function data_get($url,$post_data,$method)
{
    $ch = curl_init();
    //curl初始化
    switch ($method){
        case 'GET':
    //GET模式
        break;
        
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, 1);
            //设置curl需要POST请求
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            //设置curl相关要POST的数据
            break;
   }
   
    curl_setopt($ch, CURLOPT_URL, $url); 
    //设置curl的url地址
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $output = curl_exec($ch); 
    //以下为转码，防中文乱码
    if(! mb_check_encoding($output, 'utf-8')) {
        $output = mb_convert_encoding($output,'UTF-8',['ASCII','UTF-8','GB2312','GBK']);
    }
    curl_close($ch);
    
    return $output;
}

/* 
   此模块已经弃用
   模块B：获取上海交通系统中的某一线路的线路ID
   接口来源：浦东交通云
   相关参数：
   1.roadline 线路名称
   2.line_id  线路ID号
*/

function get_line_id($roadline)
{
    $url = "http://180.168.57.114:8380/bsth_kxbus/GetDateHttpUtils/Getlinename.do";
    $post_data = array("linename" => $roadline);
    $result = data_get($url,$post_data,'POST');
    
    return json_decode($result,true);
}

/* 
   模块C：获取上海交通系统中的某一线路的站点信息
   接口来源：浦东交通云
   相关参数：
   1.roadline 线路名称
   2.line_id  线路ID号
*/

function stop_info($roadline,$line_id)
{
    $file = "./line/$roadline.json";
    if(!file_exists($file)){   //判断线路站点是否已经被缓存
    $url = "http://180.168.57.114:8380/bsth_kxbus/GetDateHttpUtils/Getlinexx.do";
    //设置post数据
    $post_data = array(
        "linename" => $roadline,
        "lineid" => $line_id
                      );
    $result = data_get($url,$post_data,'POST');
    }
    return $result;  //如缓存 此模块直接跳过，不执行
}

/* 
   模块DA：站点数据格式化处理
   相关参数：
   1.data 站点数据
*/

function station_output_A($roadline,$data)
{
    //降低api服务器压力，加快反馈速度（此处做缓存）
    $file = "./line/$roadline.json";
    if(file_exists($file)){
        $data = file_get_contents($file); //读取缓存
    }
    //如果没有缓存，那么正常将function stop_info进行数据处理
    else
    {
        $data = json_decode($data,true);
        $data = json_decode($data['linelist'],true);
        //处理原数据，转化为数组
        $data = $data['lineInfoDetails'];
        
        $sum = count($data['lineResults0']['stop']) + count($data['lineResults1']['stop']);
        //计算来回的站点总数
        $data0 = station_output_B($roadline,$data,1);
        //去程站点数据
        $data1 = station_output_B($roadline,$data,0);
        //回程站点数据
     
        $data = array_merge($data0,$data1);
        //回程去程的2个数组合并
        $data = array('Count' => $sum , 'data' => $data);
        $data = json_encode($data);
        file_put_contents($file,$data); 
        //获取的文件缓存，未来此处增加一个判断缓存有效性的逻辑功能
    }
    return $data;
}

/* 
   模块DB：站点数据格式化处理
   相关参数：
   1.data 站点数据
*/

function station_output_B($roadline,$output,$direction)
{
    //此函数，为了与前端相匹配，从而对数据做整理
    
    /*
    模拟图相关必要参数说明：
        Upstream、Downstream 2者有且只能有一个为true,表明上下行
        sumup 分别存储上行、下行的站点个数
        LevelName 站点名称
        ToDirection 该方向的终点站名称
        LevelId 站点序列 —— 1为首项，1为公差的等差数列
        （LevelId可以通俗地理解为那一站是在该方向中的第几站）
        
    其他与模拟图非相关，仅预留的参数，测试用途：
        Stationid  在公交网系统中的站点ID
        
    */
    switch ($direction) {
        case 0:
            $Upstream = 'true';
            $Downstream = '';
            $sumup = count($output['lineResults0']['stop']);
            $finalup = $output['lineResults0']['stop'][$sumup-1]['zdmc'];
            break;
        
        case 1:
            $Upstream = '';
            $Downstream = 'true';
            $sumup = count($output['lineResults1']['stop']);
            $finalup = $output['lineResults1']['stop'][$sumup-1]['zdmc'];
            break;
    }
    
    $data = array();

    foreach ($output['lineResults'.$direction]['stop'] as $id => $zdmc){
       $data1 = array('RoadLine'=> $roadline , 'Upmax'=>$sumup, 'LevelName' => $zdmc['zdmc'] , 'LevelId' => $id+1 ,'Stationid' => $zdmc['id'],'Upstream' => $Upstream,'Downstream'=> $Downstream,'ToDirection' => $finalup);
       $x++;
       $data = array_merge($data,array($x=>$data1));
       //数据合并
    }

    return $data;
}

/* 
   模块E：实时公交数据格式化处理
*/

function gps_data_get($roadline,$line_start,$line_end,$line_id,$station_data)
{
    
    $json_string = file_get_contents('./nbbm2PlateNo.json');
    $arr = json_decode($json_string,true);
    $info_driver = drivername($line_id);
    //去程
    $url = "http://180.166.5.82:9777/gps/findByLineAndUpDown?lineCode=$line_id&upDown=0";
    $data = json_decode(data_get($url,'','GET'),true);
    //计算配车
    $jhpc = count($data['list']);
    //计算当前运营车辆
/*
    foreach ($data['forecast'] as $k4 => $v4)
    {
        if ($v4 <> 0) $dqyy++; //运营里程不为0 则表明车辆正在运营
    }
*/
    $file = "./pc/$roadline.json";
        if(file_exists($file)){
        $data2 = file_get_contents($file); //读取缓存
        $data2 = json_decode($data2,true);
        }
    $base_data = array ('jhpc'=>$jhpc,'dqyy'=>$jhpc);

    //初始化
    $x = 0;
    $res = array();
    $arr3 = first_stop($line_id,0,$station_data);
    //print_r($arr3);
    foreach ($data['list'] as $order => $v_info)
    {
        
         //将自编转化为车牌
            foreach($arr as $t1 => $t2)
        {
                if ($t1 == $v_info['nbbm'] ) $res[$x]['vid'] = $t2;
        }
        
        $res[$x]['vnumber'] = $v_info['nbbm'];
        //自编号
        
        $res[$x]['todir'] = 1;
        //方向
        
        $res[$x]['Speed'] = round($v_info['speed']);
        //车辆速度（GPS计算）
        
        $upmax = $station_data['data'][1]['Upmax'];
        //获取上行站点个数
        
        if ( $v_info['seconds'] < 60 ) {$res[$x]['dzsj'] = '剩余'.$v_info['seconds'].'秒 '.$v_info['distance'].'米';}
        else
        {
            $res[$x]['dzsj'] = '剩余'.round($v_info['seconds']/60) .'分钟 '.$v_info['distance'].'米';
        }
        //公交车到站预测
        
        if ($v_info['distance'] <210) $res[$x]['dzsj'] ='即将进站';
        
        $res[$x]['lpname'] = $v_info['sch']['lpName'].'路牌 '.$v_info['sch']['fcsj'];
        
        $res[$x]['DWTime'] = date('Y-m-d H:i:s', $v_info['serverTimestamp']/1000);
        //车上设备的定位时间戳格式转换
        
        if ($v_info['distance'] > 600) $v_info['distance'] = 600;
        if ($v_info['distance'] < 0) $v_info['distance'] =0;

        $res[$x]['rate'] = -(600-$v_info['distance'])/600*1.25+0.05;
        
        
        //该段功能为根据站点ID、名字查找现在车辆行驶在第几站（nextlevel = nowlevel + 1）
        foreach ($station_data['data'] as $xl => $zd)
        {
            if($zd['Stationid'] == $v_info['stationCode']) $res[$x]['nextlevel'] = $xl+2-$upmax;
        }
        
        //判断是否在最后一站

        if ($res[$x]['nextlevel']+2 == $upmax) $rate = 0;

        
        $res[$x]['drivername'] = $res[$x]['lpname'];
        
        if ($v_info['sch']['bcType'] == 'region') $res[$x]['drivername'] = '区间'.$res[$x]['lpname'].' '.$v_info['sch']['remarks'];

        $res[$x]['state'] = '营运车辆';
        //默认认为所有车都是运营车辆
        
               //echo $res[$x]['vnumber'].' '.$res[$x]['nextlevel'].' ';
 
        if ($res[$x]['nextlevel'] == 2) {
            $res[$x]['state'] = $v_info['stationName'];
            //先把所有有可能在终点站的车，状态全部定义为在终点站
            foreach ($arr3 as $k1 => $k2)
            {
                //如果发现此车在第一站的运营车辆中，将其重新划分为营运车辆
                if ($k2 == $res[$x]['vid']) {$res[$x]['state'] = '营运车辆';}
            }
        }

        $v_info['seconds2'] = -$v_info['seconds2'];
        //if ((empty($v_info['distance']) or $v_info['seconds2'] > 30 or !empty($v_info['parkCode'] and (time() - $v_info['serverTimestamp']/1000) > 120)) and empty($v_info['sch']['lpName']) and $res[$x]['state'] <> $v_info['stationName'] or $v_info['sch']['inOut'] == 'true'){$res[$x]['state'] = '停车场';$sjyy++;}
        
        if (!empty($v_info['sch']['lpName']))
        {
            if($v_info['sch']['inOut'] == 'true') {$res[$x]['state'] = '停车场';$sjyy++;}
        }
        elseif (empty($v_info['stationName'])) {
            $res[$x]['state'] = '停车场';
            $sjyy++;
        }
        elseif(empty($v_info['distance']) or $v_info['seconds2'] > 30 or (time() - $v_info['serverTimestamp']/1000) > 90){
            $res[$x]['state'] = '停车场';
            $sjyy++;
        }

        /*
            相关参数说明：
                        $v_info['distance'] 离下一站距离（m）
                        $v_info['seconds2'] 到达下一站所需时间（s）
                        $v_info['lpName']   路牌信息
                        
            逻辑判断说明：            
            对于车辆是否为运营状态的判断主要在于对预计到达时间、路牌目的地、是否有停车场标签判定
            判断逻辑为：
                优先判断本车有没有路牌，如果有路牌且终点不为停车场那么就不再继续判断直接纳入运营车辆
                                        如果有路牌且终点为停车场那么就不再继续判断直接纳入停车场状态（非运营车）
                                        
                如果没有路牌：
                            判断是否有停车场标签(parkCode)，如果有就不再继续判断直接纳入停车场状态
                                                            如果没有 判断是否有distance 并且 seconds2 > -30s ***
                                                                     如果2个条件都满足则纳入运营车辆
                                                                     否则纳入停车场状态（非运营车）
                                                                     
                *** 由于浦交系统的设计缺陷，seconds2可能会因为gps偏差导致为负值，但是运营车一定大于-30s
                    部分时候车辆网络会掉线，为了防止车辆一直滞留在一站引起误导，超时2分钟即从模拟图上移除
            
        
        
            模拟图必须的可常值参数，此为默认值，如无必要请勿修改
            
            rate 表明当前车辆在行驶在2站之间的哪个位置 rate∈ [0,1]
                1为已经到达下一站 该段路程行驶完成
                0为刚刚到达本站 该段行驶路程开始 默认为0
                箭头的判断随每辆车的rate值决定
                注意：此值在手机版模拟图已经废弃，但是在电脑版巴士通依旧正常
                
                当然你可以通过各类信息源使得rate值可以被算出，请在此处添加rate计算代码
                
            stationid 此值已经废弃 但是如果不赋予默认值模拟图会报错
            
        */
        $res[$x]['stationid'] = -1;
        $res[$x]['rate'] = 0;
        
        
        $res[$x]['wz'] =  $station_data['data'][$res[$x]['nextlevel']+$upmax-1]['LevelName'];
        //展现当前位置（离本车最近的站点）
        
        $x++;
        //累加器
    }
    
    //回程
    $url = "http://180.166.5.82:9777/gps/findByLineAndUpDown?lineCode=$line_id&upDown=1";
    $data = json_decode(data_get($url,'','GET'),true);
    
    $arr3 = first_stop($line_id,1,$station_data);
    foreach ($data['list'] as $order => $v_info)
    {
        
        //将自编转化为车牌
        foreach($arr as $t1 => $t2)
        {
                if ($t1 == $v_info['nbbm'] ) $res[$x]['vid'] = $t2;
        }
        
        
        $res[$x]['vnumber'] = $v_info['nbbm'];
        //自编号
        $res[$x]['todir'] = 0;
        //方向
        $res[$x]['Speed'] = round($v_info['speed']);
        //速度
        $res[$x]['DWTime'] = date('Y-m-d H:i:s', $v_info['serverTimestamp']/1000);
        //设备时间戳转化
        
        if ( $v_info['seconds'] < 60 ) {$res[$x]['dzsj'] = '剩余'.$v_info['seconds'].'秒 '.$v_info['distance'].'米';}
        else
        {
            $res[$x]['dzsj'] = '剩余'.round($v_info['seconds']/60) .'分钟 '.$v_info['distance'].'米';
        }
        //到站预测
        
        if ($v_info['distance'] <210) $res[$x]['dzsj'] ='即将进站';
        //进站提醒
        
         $res[$x]['lpname'] = $v_info['sch']['lpName'].'路牌 '.$v_info['sch']['fcsj'];
        //路牌信息暂时不展示在模拟图中
        
      
        if ($v_info['distance'] > 600) $v_info['distance'] = 600;
        if ($v_info['distance'] < 0) $v_info['distance'] =0;

        $res[$x]['rate'] = (600-$v_info['distance'])/1200*1.2; 
        //站中进度条
    
        $res[$x]['drivername'] = $res[$x]['lpname'];//$v_info['sch']['jName'].' '.$res[$x]['lpname'];
        if ($v_info['sch']['bcType'] == 'region') $res[$x]['drivername'] = '区间'.$res[$x]['lpname'].' '.$v_info['sch']['remarks'];

        
        foreach ($station_data['data'] as $xl => $zd)
        {
            if($zd['Stationid'] == $v_info['stationCode']) {$res[$x]['nextlevel'] = $xl+2;break;}
        }
        
        //判断是否到达最后一站
        if ($res[$x]['nextlevel'] == $upmax) $rate =0;
        
        $res[$x]['state'] = '营运车辆';
        $v_info['seconds2'] = -$v_info['seconds2'];
        //if ( ($v_info['seconds2']>200 and !empty($v_info['sch']['lpName']) or strpos($v_info['sch']['zdzName'],'停车')!==false)){$res[$x]['state'] = '停车场';$sjyy++;} 

        if (!empty($v_info['sch']['lpName']))
        {
            if($v_info['sch']['inOut'] == 'true') {$res[$x]['state'] = '停车场';$sjyy++;}
        }
        elseif (empty($v_info['stationName'])) {
            $res[$x]['state'] = '停车场';
            $sjyy++;
        }
        elseif(empty($v_info['distance']) or $v_info['seconds2'] > 30 or (time() - $v_info['serverTimestamp']/1000) > 100){
            $res[$x]['state'] = '停车场';
            $sjyy++;
        }        
        $res[$x]['stcsecond'] = $v_info['seconds2'];

        if ($res[$x]['nextlevel'] == 2) {
            $res[$x]['state'] = $v_info['stationName'];
                        
            foreach ($arr3 as $k1 => $k2)
            {
                if ($k2 == $res[$x]['vid']) $res[$x]['state'] = '营运车辆';
            }
        }        
        //整理结束
        
        $res[$x]['stationid'] = -1;
        $res[$x]['rate'] = 0;
        $res[$x]['wz'] =  $station_data['data'][$res[$x]['nextlevel']-1]['LevelName'];
        $x++;
        
    }
    
    $base_data['dqyy'] = $base_data['dqyy'] + count($data['list']) - $sjyy;
    //echo $sjyy;
    $base_data['jhpc'] = $base_data['jhpc'] + count($data['list']);
    //echo $data2['jhpc'];
    if ($data2['jhpc'] > $base_data['jhpc']) {$base_data['jhpc'] = $data2['jhpc'];}
    $base_data = json_encode($base_data);
    file_put_contents($file,$base_data);
    
    //并入数组并返回值
    $res = array('Count' => $x+$x1 , 'data' => $res);
    return $res; 
}


/* 
   2019-06-17 Update
   模块F：实时公交发车时间处理
*/

function departscreen($line_id,$roadline)
{
    $json_string = file_get_contents('./nbbm2PlateNo.json');
    //读取车牌与自编号信息文件
    
    $arr = json_decode($json_string,true);
    

    /*去程*/
    $url = "http://180.166.5.82:9777/xxfb/getdispatchScreen?lineid=$line_id&direction=0&t=";
    //此为发车时间获取的API
    $data = data_get($url,'','GET');
    $xml = simplexml_load_string($data);
    //由于获得的数据为XML类型，需要转化为Json后才能顺利转化为数组
    $data = json_decode(json_encode($xml),TRUE);
    //将转化后的Json进一步转化为数组
    
    $data =  $data['cars']['car'];
    $x = 0;
    
    //将车牌转化为自编
    foreach($arr as $t1 => $t2)
        {
                if ($t2 == $data[0]['vehicle'] ) $d0_car = $t1;
                if (empty($data[0]['time'])) { if ($t2 == $data['vehicle'] ) $d0_car = $t1;}
        }
        

        $jhjg = (strtotime($data[1]['time']) - strtotime($data[0]['time']))/60;
        //第二辆车的发车时间减去第一辆车的发车时间为计划发车时间
        if ($jhjg < 0) $jhjg = 0;
        $yjjg = (strtotime($data[2]['time']) - strtotime($data[1]['time']))/60;
        //第三辆车的发车时间减去第二辆车的发车时间为预计发车时间
        if ($yjjg < 0) $yjjg = $jhjg;
        if (empty($data[0]['time'])) {$data[0]['time'] = $data['time'];}
        
        //if (empty($d0_car)) $d0_car = 'SQL Error';

        $data0 = array('dir'=>1,'VEHICLENUMBERING'=>$d0_car,'PLANTIME'=>$data[0]['time'],'jhjg'=>$jhjg,'yjjg'=>$yjjg );
        //发车时间整理，封装成模拟图能正常读取的API
        
        
    /*回程*/
    $url = "http://180.166.5.82:9777/xxfb/getdispatchScreen?lineid=$line_id&direction=1&t=";
    $data = data_get($url,'','GET');
    $xml = simplexml_load_string($data);
    $data = json_decode(json_encode($xml),TRUE);
    $data =  $data['cars']['car'];
    $x = 0;
    foreach($arr as $t1 => $t2)
        {
                if ($t2 == $data[0]['vehicle'] ) $d1_car = $t1;
                if (empty($data[0]['time'])) { if ($t2 == $data['vehicle'] ) $d1_car = $t1;}
        }

        $jhjg = (strtotime($data[1]['time']) - strtotime($data[0]['time']))/60;
        if ($jhjg < 0) $jhjg = 0;
        $yjjg = (strtotime($data[2]['time']) - strtotime($data[1]['time']))/60;        
        if ($yjjg < 0) $yjjg = $jhjg;
        if (empty($data[0]['time'])) $data[0]['time'] = $data['time'];
        
        //if (empty($d1_car)) $d1_car = 'SQL Error';

        $data1 = array('dir'=>0,'VEHICLENUMBERING'=>$d1_car,'PLANTIME'=>$data[0]['time'],'jhjg'=>$jhjg,'yjjg'=>$yjjg );
        
        $data2['jhpc']=0;$data2['dqyy']=0;
        $file = "./pc/$roadline.json";
        if(file_exists($file)){
        $data2 = file_get_contents($file); //读取缓存
        $data2 = json_decode($data2,true);
        }
        $data = array('Count'=>2,'jhpc'=>$data2['jhpc'],'dqyy'=>$data2['dqyy'],'data'=>array($data1,$data0));
    return json_encode($data);

}

/* 
   2019-06-26 Update
   模块G：修复车辆首站不显示的问题
*/
function first_stop($line_id,$direction,$station_data)
{

    if ($direction == 1) {$station = $station_data['data'][1]['Stationid'];}
    else{
        $upmax = $station_data['data'][0]['Upmax'];
        $station = $station_data['data'][$upmax+1]['Stationid'];
    }
    $url = "http://180.166.5.82:9777/xxfb/carMonitor?lineid=$line_id&stopid=$station&direction=$direction&t=";
    $data = data_get($url,'','GET');
    $xml = simplexml_load_string($data);
    $data = json_decode(json_encode($xml),TRUE);
    $data = $data['cars']['car'];
    $d1_car = array();
    if (!empty(($data[1]))){
    foreach ($data as $e1 => $e2){
        $d1_car[$e1] = $e2['terminal'];
    }}else{
        $d1_car[0] = $data['terminal'];
    }
    return $d1_car;
}


/*
    W:Experimental Code
    Please do not use it in productional Mode
*/
function drivername($code)
{
    $filePath='staff.csv';
    $lines = array_map('str_getcsv', file($filePath));; 

    $result = array();
    $headers = null;

    if (count($lines) > 0) {
        $headers = $lines[0];
    }

    for($i=1; $i<count($lines); $i++) {
        $obj = $lines[$i];
        //$obj = iconv('gbk','utf-8',$obj);
        // $obj = trimStr($obj);
        $result[] = array_combine($headers, $obj);// 成数组
    }

    //print_r($result[0]);
    foreach ($result as $a => $b)
    {
        if ($b['number'] == $code) return $b['name'];
    }
}

/*
    记录访问者的IP以及查询的线路名、访问时间
*/
function logResult($word='',$roadline,$function) 
{
    $fp = fopen("log.txt","a");
    flock($fp, LOCK_EX) ;
    fwrite($fp,' | '.strftime("%Y-%m-%d %H:%M:%S",time())." | IP:".$word." | $roadline"." | ".$function."\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}    
    //获取用户IP地址
    $ip = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
    $ip = ($ip) ? $ip : $_SERVER["REMOTE_ADDR"];



$function = $_GET['Method'];

$roadline = $_GET['roadline'];
$roadline=  strtr("$roadline","%","\\");
$rzw = '{"zw":'.'"'.$roadline.'"'."}";
$rzw = json_decode($rzw);
$roadline = $rzw -> zw ; 

//检查是否为数字线路
    if(preg_match("/^\d*$/",$roadline)) $shuzi = true;
        if ($shuzi){
        $roadline = $roadline.'路';
        }else
        {
            $roadline = $roadline;
        }
    logResult($ip,$roadline,$function);
check($roadline,$function);

