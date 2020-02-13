<?php  
$host = '45.64.11.3';  
$user = 'trainee';  
$pass = 'trai@123';  
$dbname = 'training';  
   
$conn = mysqli_connect($host, $user, $pass,$dbname);  
if(!$conn){  
  die('Could not connect: '.mysqli_connect_error());  
}  
echo 'Connected successfully<br/>';
echo '<br>';
$fo=fopen("mail.json","r");
$fr=fread($fo,filesize("mail.json"));
$array=json_decode($fr, true);
$fullstring=$array['body'];

$parsed = get_string_between($fullstring, '--Apple-Mail=_B0D804AF-968A-4092-BF8E-64ADE7AC413F', '--Apple-Mail=_B0D804AF-968A-4092-BF8E-64ADE7AC413F--');
// print_r($parsed);

function get_string_between($string, $start, $end){
  $string = ' ' . $string;
  $ini = strpos($string, $start);
  if ($ini == 0) return '';
  $ini += strlen($start);
  $len = strpos($string, $end, $ini) - $ini;
  return substr($string, $ini, $len);
}

$parsed1 = get_string_between($parsed, 'charset=us-ascii', '--Apple-Mail=_B0D804AF-968A-4092-BF8E-64ADE7AC413F');
$NEWLINE_RE = '/(\r\n)|\r|\n|=/'; // take care of all possible newline-encodings in input file
$parsed1 = preg_replace($NEWLINE_RE,'', $parsed1);
// print_r($parsed1);

$parsed2 = get_string_between($fullstring, 'Content-Id:', '--Apple-Mail=_B0D804AF-968A-4092-BF8E-64ADE7AC413F--');
// print_r($parsed2);

$parsed2 = mysqli_real_escape_string($conn, $parsed2);
$parsed1 = mysqli_real_escape_string($conn, $parsed1);
$array['header']['subject'][0] = mysqli_real_escape_string($conn, $array['header']['subject'][0]);

$query= "INSERT into training.Ashish_header1(h_from,h_subject,h_to,h_cc,h_bcc,h_date,b_message,b_source)
values('".$array['header']['from'][0]."','".$array['header']['subject'][0]."','".$array['header']['to'][0]."',
'".$array['header']['cc'][0]."','".$array['header']['bcc'][0]."',
'".$array['header']['date'][0]."','".$parsed1."','".$parsed2."')";
 
mysqli_query($conn,$query);

$sql="SELECT * from training.Ashish_header1";
$run=mysqli_query($conn,$sql);

$res=mysqli_fetch_array($run);


// while($res=mysqli_fetch_array($run))
// {

 echo 'From: '.$res['h_from'];
 echo '<br>';
 echo 'Subject: '.$res['h_subject'];
 echo '<br>';
 echo 'To: '.$res['h_to'];
 echo '<br>';
 echo 'Cc: '.$res['h_cc'];
 echo '<br>';
 echo 'Bcc: '.$res['h_bcc'];
 echo '<br>';
 echo 'Date: '.$res['h_date'];
 echo '<br>';
 echo  $res['b_message'];
 echo '<br>';
 header("Content-type: image/png");
//  $data = base64_decode(substr($res['b_source'],23));
//  echo $data;
 echo '<img src="data:image/png;base64,'.substr($res['b_source'],23).'"/>';

// }
if ($query) {
  
  echo '<br>';
    echo "New record created successfully";
} else {
  echo '<br>';
    echo "Error: Query Is Not Being Perform " . $query . "<br>" . $conn->error;
}
mysqli_close($conn);  
 
?>