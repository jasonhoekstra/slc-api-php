<?
require_once 'slcapiwrapper.php';
$api = new slcAPIWrapper();

$apiCalls = array('student', 'students', 'sections', 'attendances', 'courses', 'reportCards', 'teacher', 'parents', 'studentAssessments');

function json_format($json) {
  $tab = "  ";
  $new_json = "";
  $indent_level = 0;
  $in_string = false;

  $json_obj = json_decode($json);

  if ($json_obj === false)
    return false;

  $json = json_encode($json_obj);
  $len = strlen($json);

  for ($c = 0; $c < $len; $c++) {
    $char = $json[$c];
    if ($char == '\\' && $c + 1 < $len && $json[$c + 1] != '\\')
      $char = '';

    switch ($char) {
      case '{':
      case '[':
        if (!$in_string) {
          $new_json .= $char . "\n" . str_repeat($tab, $indent_level + 1);
          $indent_level++;
        }
        else {
          $new_json .= $char;
        }
        break;
      case '}':
      case ']':
        if (!$in_string) {
          $indent_level--;
          $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
        }
        else {
          $new_json .= $char;
        }
        break;
      case ',':
        if (!$in_string) {
          $new_json .= ",\n" . str_repeat($tab, $indent_level);
        }
        else {
          $new_json .= $char;
        }
        break;
      case ':':
        if (!$in_string) {
          $new_json .= ": ";
        }
        else {
          $new_json .= $char;
        }
        break;
      case '"':
        if ($c > 0 && $json[$c - 1] != '\\') {
          $in_string = !$in_string;
        }
      default:
        $new_json .= $char;
        break;
    }
  }

  return $new_json;
}
?>  
<html>
  <head>
    <script src="codemirror/lib/codemirror.js"></script>
    <link rel="stylesheet" href="codemirror/lib/codemirror.css">
    <script src="codemirror/mode/javascript/javascript.js"></script>
    <style type="text/css">
      .CodeMirror-scroll { height: 600px; }
      .CodeMirror {border-top: 1px solid black; border-bottom: 1px solid black;}
    </style>
  </head>
  <body style='font-family: verdana, serif;'>  
    <div style='padding: 1%; width: 60%; margin: 0; border: 2px solid #999;'>
      <div style='text-align: center; font-size: 1.1em; font-weight: bold'>SLC API Test Page</div>
      <form action='' method='post' style='width: 200px; margin: 0 auto;'>
        <select name='apiCall'>
          <option value=''>Select API Call...</option>
          <? foreach ($apiCalls as $call) {
            ?><option <? if (isset($_POST['apiCall']) && $call == $_POST['apiCall']) { ?>selected='selected'<? } ?> value='<?= $call ?>'>
              <?= $call ?>
            </option> <? }
            
            $uuid = $_POST['uuid'];
            ?>
        </select>
        <span style="font-size:75%">UUID:</span> <input type="text" size="60" name="uuid" value="<?= $uuid ?>" />
        <br /><br />
        <button style='width: 100px;' type='submit'>Make API Call</button>
      </form>
    </div>


    <? if (isset($_POST['apiCall']) && $_POST['apiCall']) { ?>
      <div>
        <h3>API Response:</h3>
        <textarea id="codeEditor" rows="80" cols="120" wrap="off" style="height:500px; border: solid black" >
                  
          <?

          switch ($_POST['apiCall']) {
            case 'student':
              $result = $api->getStudent($uuid);
              break;
            case 'students':
              $result = $api->getAllStudents();
              break;
            case 'sections':
              $result = $api->getAllSections();
              break;
            case 'attendances':
              $result = $api->getAttendances($uuid);
              break;
            case 'courses':
              $result = $api->getAllCourses();
              break;
            case 'reportCards':
              $result = $api->getAllReportCards();
              break;
            case 'teacher':
              $result = $api->getTeacher($uuid);
              break;
            case 'parents':
              $result = $api->getParents();
              break;
            case 'studentAssessments':
              $result = $api->getAllStudentAssessments();
              break;
          }

          echo json_format(json_encode($result));
          ?></textarea></div><?
        ;
      }
        ?>

    <script>
      var editor = CodeMirror.fromTextArea(document.getElementById("codeEditor"), {
        lineNumbers: true
      });
    </script>
  </body>
</html>