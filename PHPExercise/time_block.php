<?php


$path = "/Users/yugang/Downloads/export_timeblock.csv";
$beginDate = '2018-07-01';
$endDate = '2019-08-01';
if ($argc >= 3) {
	$beginDate = $argv[1];
	$endDate = $argv[2];
	isset($argv[3]) && $path = $argv[3];
}
$outputPath = "/Users/yugang/Downloads/export_timeblock_" . $beginDate . "~" . $endDate . ".csv";
var_dump($beginDate, $endDate, $path, $outputPath);

$conn = mysqli_connect('127.0.0.1', 'root', 'k343ks4s', 'study');
if (!$conn) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

$conn->query("set names utf8");
// 读取记录并插入数据库
if (false && ($handle = fopen($path, "r")) != FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) != FALSE) {
		// 过滤不满足条件记录
		if (strtotime($data[0]) < strtotime($beginDate) || strtotime($data[0]) >= strtotime($endDate)) {
			continue;
		}
		
		// 解析数据
		$beginTime = $data[0];
		$endTime = date('Y-m-d H:i', (strtotime($data[0]) + $data[1] * 60));
		$durationTime = $data[1];
		$eventMainType = $data[2];
		$eventType = $data[3];
		$contentType = $data[4];
		$content = $data[5];
		$remark = $data[6];
		$tag = $data[7];
		$status = $data[8] == '已完成' ? 1 : 0;

		// 构建SQL插入数据
		$inserSql = sprintf("INSERT INTO `time_block` (begin_time, end_time, duration_time, event_main_type, event_type, content_type, content, remark, tag, status) VALUES('%s', '%s', %d, '%s', '%s', '%s', '%s', '%s', '%s', %d)", $beginTime, $endTime, $durationTime, $eventMainType, $eventType, $contentType, $content, $remark, $tag, $status);
		echo $status . "::" . $inserSql . "<br/>\n";
		$result = $conn->query($inserSql);
	}

	fclose($handle);
}



// 输出当月内容
$exportSql = "select begin_time, duration_time, event_type, content, remark from time_block where  begin_time >= '{$beginDate}' and begin_time < '{$endDate}' ";
$retval = $conn->query($exportSql);
if (! $retval) {
	die('无法获取数据：' . mysqli_error($conn));
}

$outputFileHandle = fopen($outputPath, 'w');
// fputcsv($outputFileHandle, ['开始时间', '持续时间', '事件类别', '事件内容', '备注']);
$header = ['开始时间', '持续时间', '事件类别', '事件内容', '备注'];
fputs($outputFileHandle, iconv('UTF-8', 'GBK//TRANSLIT', implode(",", $header) . "\n"));
while ($row = mysqli_fetch_array($retval, MYSQLI_ASSOC)) {
	//fputcsv($outputFileHandle, $row);
	fputs($outputFileHandle, iconv('UTF-8', 'GBK//TRANSLIT', implode(",", $row). "\n"));
}
fclose($outputFileHandle);

// 统计
$statByTypeSql = "select sum(`duration_time`) / 60 as `total_time`,`event_type` from time_block where begin_time >= '{$beginDate}' and begin_time < '{$endDate}' group by  `event_type` order by `total_time` desc";
$statByTypeval = $conn->query($statByTypeSql);
echo "耗时 ：事件类别\n";
while ($row = mysqli_fetch_array($statByTypeval, MYSQLI_ASSOC)) {
	echo $row['total_time'] . " : " . $row['event_type'] . "\n";
}

// 统计
$statByContentSql = "select sum(`duration_time`) / 60 as `total_time`,`content` from time_block where begin_time >= '{$beginDate}' and begin_time < '{$endDate}'
and event_type in ('学习', '阅读') group by  `content` order by `total_time` desc";
$statByContentval = $conn->query($statByContentSql);
echo "耗时 ：事件内容\n";
while ($row = mysqli_fetch_array($statByContentval, MYSQLI_ASSOC)) {
	echo $row['total_time'] . " : " . $row['content'] . "\n";
}

// 关闭连接
$conn->close();
