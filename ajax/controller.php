<?php


/*
    2019-06-17 浦交模拟图V2 Beta
    Made by Leo
    
    代码可理解程度：非常简单
    需要阅读时间：7-20分钟
*/

function check($roadline,$function)
{
    $line_info = get_line_id($roadline);
    $line_id = $line_info['line']['line_id'];
    $line_start = $line_info['line']['start_stop'];
    $line_end = $line_info['line']['end_stop'];
    
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

            $depart_time = departscreen($line_id);
            print_r($depart_time);
            break;
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
    switch ($method){
        case 'GET':

           break;
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            break;
   }
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $output = curl_exec($ch); 
    //转码，防中文乱码
    if(! mb_check_encoding($output, 'utf-8')) {
        $output = mb_convert_encoding($output,'UTF-8',['ASCII','UTF-8','GB2312','GBK']);
    }
    curl_close($ch);
    
    return $output;
}

/* 
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
    $url = "http://180.168.57.114:8380/bsth_kxbus/GetDateHttpUtils/Getlinexx.do";
    //设置post数据
    $post_data = array(
        "linename" => $roadline,
        "lineid" => $line_id
                      );
    $result = data_get($url,$post_data,'POST');
    
    return $result;
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
    //如果没有缓存，那么正常获取
    else
    {
        $data = json_decode($data,true);
        $data = json_decode($data['linelist'],true);
        $data = $data['lineInfoDetails'];
        //把Json格式转换成数组
        $sum = count($data['lineResults0']['stop']) + count($data['lineResults1']['stop']);

        $data0 = station_output_B($roadline,$data,1);
        $data1 = station_output_B($roadline,$data,0);
     
        $data = array_merge($data0,$data1);
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
    //去程
    $url = "http://180.166.5.82:9777/gps/findByLineAndUpDown?lineCode=$line_id&upDown=0";
    $data = json_decode(data_get($url,'','GET'),true);
    //初始化
    $x = 0;
    $res = array();
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
        
        $res[$x]['Speed'] = $v_info['speed'];
        //车辆速度（GPS计算）
        
        $upmax = $station_data['data'][1]['Upmax'];
        //获取上行站点个数
        
        $res[$x]['DWTime'] = date('Y-m-d H:i:s', $v_info['serverTimestamp']/1000);
        //车上设备的定位时间戳格式转换
        
        
        //该段功能为根据站点ID、名字查找现在车辆行驶在第几站（nextlevel = nowlevel + 1）
        foreach ($station_data['data'] as $xl => $zd)
        {
            if($zd['LevelName'] == $v_info['stationName']) $res[$x]['nextlevel'] = $xl+2-$upmax;
        }
        
        
        
        $res[$x]['state'] = '营运车辆';
        //默认认为所有车都是运营车辆
        
        if ($v_info['seconds2'] <0){$res[$x]['state'] = '停车场';}  
        //异常的时间都是由于车辆不在线路上导致，大部分原因都是因为前往停车场，此类都归于停车场
        
        if ($res[$x]['nextlevel'] == 2) $res[$x]['state'] = $v_info['stationName'];
        //整理结束
        
        /*
            模拟图必须的可常值参数，此为默认值，如无必要请勿修改
            
            rate 表明当前车辆在行驶在2站之间的哪个位置 rate∈ [0,1]
                1为已经到达下一站 该段路程行驶完成
                0为刚刚到达本站 该段行驶路程开始 默认为0
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
        $res[$x]['Speed'] = $v_info['speed'];
        //速度
        $res[$x]['DWTime'] = date('Y-m-d H:i:s', $v_info['serverTimestamp']/1000);
        //设备时间戳转化
        
        
        foreach ($station_data['data'] as $xl => $zd)
        {
            if($zd['Stationid'] == $v_info['stationCode']) {$res[$x]['nextlevel'] = $xl+2;break;}
        }
        $res[$x]['state'] = '营运车辆';
        if ($v_info['seconds2'] <0){$res[$x]['state'] = '停车场';}
        if ($res[$x]['nextlevel'] == 2 ) $res[$x]['state'] = $v_info['stationName'];
        //整理结束
        
        $res[$x]['stationid'] = -1;
        $res[$x]['rate'] = 0;
        $res[$x]['wz'] =  $station_data['data'][$res[$x]['nextlevel']-1]['LevelName'];
        $x++;
        
    }
    //并入数组并返回值
    $res = array('Count' => $x+$x1 , 'data' => $res);
    return $res; 
}


/* 
   2019-06-17 Update
   模块F：实时公交发车时间处理
*/

function departscreen($line_id)
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
        }

        $jhjg = (strtotime($data[1]['time']) - strtotime($data[0]['time']))/60;
        //第二辆车的发车时间减去第一辆车的发车时间为计划发车时间
        $yjjg = (strtotime($data[2]['time']) - strtotime($data[1]['time']))/60;
        //第三辆车的发车时间减去第二辆车的发车时间为预计发车时间
        
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
        }

        $jhjg = (strtotime($data[1]['time']) - strtotime($data[0]['time']))/60;
        $yjjg = (strtotime($data[2]['time']) - strtotime($data[1]['time']))/60;
        $data1 = array('dir'=>0,'VEHICLENUMBERING'=>$d1_car,'PLANTIME'=>$data[0]['time'],'jhjg'=>$jhjg,'yjjg'=>$yjjg );

        $data = array('Count'=>2,'data'=>array($data1,$data0));
    return json_encode($data);

}

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

check($roadline,$function);

