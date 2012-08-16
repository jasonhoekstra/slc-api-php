<?
require_once 'slcapiwrapper.php';
$api = new slcAPIWrapper();

function json_format($json)
{
    $tab = "  ";
    $new_json = "";
    $indent_level = 0;
    $in_string = false;

    $json_obj = json_decode($json);

    if($json_obj === false)
        return false;

    $json = json_encode($json_obj);
    $len = strlen($json);

    for($c = 0; $c < $len; $c++)
    {
        $char = $json[$c];
        if ($char == '\\' && $c + 1 < $len && $json[$c+1] != '\\') $char='';
        
        switch($char)
        {
            case '{':
            case '[':
                if(!$in_string)
                {
                    $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
                    $indent_level++;
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case '}':
            case ']':
                if(!$in_string)
                {
                    $indent_level--;
                    $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case ',':
                if(!$in_string)
                {
                    $new_json .= ",\n" . str_repeat($tab, $indent_level);
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case ':':
                if(!$in_string)
                {
                    $new_json .= ": ";
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case '"':
                if($c > 0 && $json[$c-1] != '\\')
                {
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
</head>
  <body style='font-family: verdana, serif;'>  
    <?
    if (isset($_POST['apiCall']) && $_POST['apiCall']) {
      printTestForm();
      ?><div><h3>API Response:</h3><textarea rows="40" cols="120" wrap="off" style="overflow: scroll; overflow-y: scroll; overflow-x: scroll; overflow:-moz-scrollbars-vertical;"><?
    if ($_POST['apiCall'] == 'student')
      $result = $api->getStudent('2012an-b7022d67-bcf8-11e1-8506-0247757a1887');
    elseif ($_POST['apiCall'] == 'students')
      $result = $api->getAllStudents();
    elseif ($_POST['apiCall'] == 'sections')
      $result = $api->getAllSections();
    elseif ($_POST['apiCall'] == 'attendances')
      $result = $api->getAttendances('2012ar-aed81718-e186-11e1-9ae2-024775652e7d');
    elseif ($_POST['apiCall'] == 'courses')
      $result = $api->getAllCourses();
    elseif ($_POST['apiCall'] == 'reportCards')
      $result = $api->getAllReportCards();
    elseif ($_POST['apiCall'] == 'teacher')
      $result = $api->getTeacher('2012ye-b002dc0c-e186-11e1-9ae2-024775652e7d');
    elseif ($_POST['apiCall'] == 'parents')
      $result = $api->getParents();
    elseif ($_POST['apiCall'] == 'studentAssessments')
      $result = $api->getAllStudentAssessments();
    elseif ($_POST['apiCall'] == 'studentAssessments - Write') {
      // generating sample StudentAssessmentAssociation JSON data object
      $assmentData = $scoreResult = array();

      // scoreResult data structure
      $scoreResult['assessmentReportingMethod'] = 'Percentile';
      $scoreResult['result'] = '90';

      // student assessment data structure
      $assmentData['administrationDate'] = '2012-08-12';
      $assmentData['administrationEnvironment'] = 'Testing Center';
      $assmentData['administrationLanguage'] = 'English';
      $assmentData['gradeLevelWhenAssessed'] = 'Ninth grade';
      $assmentData['scoreResults'][0] = $scoreResult;
      $assmentData['serialNumber'] = '01010';
      $assmentData['studentId'] = '2012ar-aed81718-e186-11e1-9ae2-024775652e7d';
      $assmentData['assessmentId'] = '2012ft-b46f0f00-e186-11e1-9ae2-024775652e7d';

      // generate JSON
      $assmentData = json_encode($assmentData);
      $result = $api->createStudentAssessment($assmentData);
    }
    //var_dump($result);
    
      echo json_format(json_encode($result));

      ?></textarea></div><?
        ;
      }
      else {
        printTestForm();
      }

      function printTestForm() {
        $apiCalls = array('student', 'students', 'sections', 'attendances', 'courses', 'reportCards', 'teacher', 'parents', 'studentAssessments', 'studentAssessments - Write');
      ?>
      <div style='padding: 1%; width: 60%; margin: 0 auto; border: 2px solid #999;'>
        <div style='text-align: center; font-size: 1.1em; font-weight: bold'>SLC API Test Page</div>
        <form action='' method='post' style='width: 200px; margin: 0 auto;'>
          <select name='apiCall'>
            <option value=''>Select API Call...</option>
            <? foreach ($apiCalls as $call) { ?><option <? if (isset($_POST['apiCall']) && $call == $_POST['apiCall']) { ?>selected='selected'<? } ?> value='<?= $call ?>'><?= $call ?></option> <? } ?>
          </select>
          <br /><br />
          <button style='width: 100px;' type='submit'>Make Call</button>
        </form>
      </div>
      <?
    }
    ?></body></html><?
    exit;
    ?>