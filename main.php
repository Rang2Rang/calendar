<?php

    define('__CORE_TYPE__','view');
    include $_SERVER['DOCUMENT_ROOT'].'/function/core.php';

?>
<!-- 년 설정-->
<select name="year" id="year">
  <option>===선택====</option>
  <?php
    for ($i = 2014; $i < 2034; $i++) {
      echo "<option>{$i}</option>";
    }
  ?>
</select>
<!-- 달 설정-->
<select name="month" id="month">
  <option>===선택====</option>
  <?php
    for ($i = 1; $i < 13; $i++) {
      echo "<option>{$i}</option>";
    }
  ?>
</select>
<button type="button" onclick="search();">이동</button>