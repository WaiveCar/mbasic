<?
include('api/common.php');
$type = aget($_GET, 'type');
if($type !== 'brief') {
  $me = me();
  if(!$me['booking_id']){
    load('/showcars.php');
  }
}
ob_start("sanitize_output");
$opts = [];
if($type == 'brief') {
  $opts['showaccount'] = false;
}
doheader('End Booking', $opts);
?>
 
<div class='box prompt'>
  <? if($type == 'brief') { ?>
    <h1>Parking Details</h1>
  <? } else { ?>
    <h1>Final Steps</h1>
  <? } ?>

  <div class=content>
    <form enctype=multipart/form-data method=post action="api/carcontrol.php?action=complete">
      <ol id=endchecklist>
        <li>When does the vehicle need to move?<br/>
        <small>Pick the soonest time it can get a ticket.</small>
          <p class='ingroup'>
            <? 
              $today = dateTz('w');
              $weekdayList = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
              $weekdayList = array_slice(array_merge($weekdayList, $weekdayList), ($today + 2), 6);
              $weekdayList[count($weekdayList) - 1] .= " (next)";
              $appendList = [
                'pm' => 'PM', 
                'am' => 'AM'
              ];
              array_unshift($weekdayList, 'Today', 'Tomorrow');
              echo "<select name=day>";
              foreach($weekdayList as $day) {
                echo "<option value=$today>$day</option>";
                $today = ($today + 1) % 7;
              }
              echo "</select>";
            ?>

            <input placeholder="ex: 3:00" class=input-inline autocomplete=off name=hours>
            <?
            foreach($appendList as $key => $unit) {
              ?><label><input type=radio name=append value=<?=$key?>><?=$unit?></input></label><?
            }
            ?>
          </p>
          <label for=nosign>
          <input id=nosign type=checkbox name=nosign>Tap here if there's no restrictions</label>
          </label>
        </li>

        <li>Take a photo of the parking sign<br/>
        <small>If there's no sign, take a photo of the intersection to show no sign is posted.</small>
          <p class='image-upload ingroup'>
            <input name=parking type=file accept="image/*;capture=camera" capture=camera/>
          </p>
          <label for=nophoto>
          <input id=nophoto type=checkbox name=nophoto>Tap here if you can't take a photo</label>
        </li>


        <? if($type != 'brief') { 
          $cnt = 3;
        ?>
          <li>Are there any new issues?<br><small>Add up to <?=$cnt?> photos</small>

          <div class='image-upload ingroup'>
            <? for($ix = 0; $ix < $cnt; $ix++) { ?>
              <input name=image<?= $ix ?> type=file accept="image/*;capture=camera" capture=camera />
            <? } ?>
          </div>
        </li>
        <? } ?>

      </ol>

      <? if($type != 'brief') { ?>
        <p>When finished, remove your belongings, close the doors, and tap "End Ride"</p>
      <? } ?>

      <input class='btn wid-1' type=submit value="End Ride">
    </form>
  </div>
</div>

</body>
</html>
