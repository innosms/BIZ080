<?

require_once 'common.php';

echo "080 수신거부 검색<br/>";

$contents = array(
	"user_number" => "0800000000",     // 080 번호
	"refusal_number" => "01012345678"  //수신거부 번호
);

$RefusalService = new RefusalService($client_id, $api_key);
$result = $RefusalService->getSearch($contents);

/******************************************************************************
- 결과값(배열)
$result['refusal'] : 존재여부 Y:있음 N:없음
$result['reg_date'] : 존재할 경우 수신거부 접수 일시
******************************************************************************/

echo "<pre>";
var_dump($result);
echo "</pre>";

?>