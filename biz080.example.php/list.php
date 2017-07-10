<?

require_once 'common.php';

echo "080 수신거부 목록<br/>";

$contents = array(
	"user_number" => "0800000000",  // 080 번호
	"search_date" => "201707",      // 조회기간 YYYY or YYYYMM or YYYYMMDD
	"list_count" => "100",          // 목록갯수 (최대 1000개까지 가능)
	"page" => "1"                   // 요청 페이지
);

$RefusalService = new RefusalService($client_id, $api_key);
$result = $RefusalService->getList($contents);

/******************************************************************************
- 결과값(배열)
$result['total_count'] : 전체 데이터 갯수
$result['data']['phone'] : 수신거부 번호
$result['data']['reg_date'] : 수신거부 접수 일시
******************************************************************************/

echo "<pre>";
var_dump($result);
echo "</pre>";

?>