<?php if (!defined('IN_PHPBB')) exit; ?>﻿<?php $this->_tpl_include('overall_header.html'); ?>

<link href="<?php echo (isset($this->_rootref['U_STYLESHEET'])) ? $this->_rootref['U_STYLESHEET'] : ''; ?>" rel="stylesheet" type="text/css" />
<div id="middlepart">
  <div style="font-style:italic;font-size:x-small;width:100%;text-align:right;"><a href="?mode=reset">Сбросить текущий тест</a></div>    
  <table cellpadding="0" cellspacing="0" width="100%" height="1">
    <tr>
      <td width="100%" align="center" valign="top" style="padding-left: 29px;"> &nbsp;
      </td>
    </tr>
  </table>
</div>

<div id="contentpart">
  <div id="content">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td class="content-box" valign="top" align="left" style="padding-right: 0;">
        <div class="content-text">
        
		<?php if (sizeof($this->_tpldata['test_contents'])) {  ?>

		<div style="float:right; width: 60%;"><h3><?php echo (isset($this->_rootref['CURRENT_TEST_CONTENTS'])) ? $this->_rootref['CURRENT_TEST_CONTENTS'] : ''; ?></h3>
			<table>
			<?php $_test_contents_count = (isset($this->_tpldata['test_contents'])) ? sizeof($this->_tpldata['test_contents']) : 0;if ($_test_contents_count) {for ($_test_contents_i = 0; $_test_contents_i < $_test_contents_count; ++$_test_contents_i){$_test_contents_val = &$this->_tpldata['test_contents'][$_test_contents_i]; ?>

				<tr class="<?php echo $_test_contents_val['TR_CLASS']; ?>">
				<th class="test_contents_th"><?php echo $_test_contents_val['HEBREW']; ?></th><td class="test_contents_td"><?php echo $_test_contents_val['TRANSLATION']; ?></td>
				</tr>
			<?php }} ?>

			</table>
		</div>
		<?php } ?>



        <form action="<?php echo (isset($this->_rootref['U_LAUNCHER_FORM_POST'])) ? $this->_rootref['U_LAUNCHER_FORM_POST'] : ''; ?>" method="post">
        <div  class="form_fieldset">
        <fieldset name="GroupLessons">
				<legend>Уроки</legend>
				<select multiple="multiple" name="lesson[]" style="width: 250px; height: 150px">
				<?php $_lesson_count = (isset($this->_tpldata['lesson'])) ? sizeof($this->_tpldata['lesson']) : 0;if ($_lesson_count) {for ($_lesson_i = 0; $_lesson_i < $_lesson_count; ++$_lesson_i){$_lesson_val = &$this->_tpldata['lesson'][$_lesson_i]; ?>

					<option value="<?php echo $_lesson_val['VALUE']; ?>" <?php if ($_lesson_val['SELECTED']) {  ?> selected="<?php echo $_lesson_val['SELECTED']; ?>"<?php } ?>><?php echo $_lesson_val['DESCRIPTION']; ?></option>
				<?php }} ?>				
				</select>
		</fieldset>
		</div>
			
        <?php $_input_count = (isset($this->_tpldata['input'])) ? sizeof($this->_tpldata['input']) : 0;if ($_input_count) {for ($_input_i = 0; $_input_i < $_input_count; ++$_input_i){$_input_val = &$this->_tpldata['input'][$_input_i]; ?>

        <div  class="form_fieldset">
        <fieldset>
		        <input <?php $_element_count = (isset($_input_val['element'])) ? sizeof($_input_val['element']) : 0;if ($_element_count) {for ($_element_i = 0; $_element_i < $_element_count; ++$_element_i){$_element_val = &$_input_val['element'][$_element_i]; ?> <?php echo $_element_val['NAME']; ?>="<?php echo $_element_val['PARAM']; ?>"<?php }} ?>><?php echo $_input_val['DESCRIPTION']; ?>

			</fieldset>
		</div>
        <?php }} $_selects_count = (isset($this->_tpldata['selects'])) ? sizeof($this->_tpldata['selects']) : 0;if ($_selects_count) {for ($_selects_i = 0; $_selects_i < $_selects_count; ++$_selects_i){$_selects_val = &$this->_tpldata['selects'][$_selects_i]; ?>

		<div class="form_fieldset">
			<fieldset>
				<legend><?php echo $_selects_val['DESCRIPTION']; ?></legend>
				<select name="<?php echo $_selects_val['GROUP_NAME']; ?>">
				<?php $_sub_count = (isset($_selects_val['sub'])) ? sizeof($_selects_val['sub']) : 0;if ($_sub_count) {for ($_sub_i = 0; $_sub_i < $_sub_count; ++$_sub_i){$_sub_val = &$_selects_val['sub'][$_sub_i]; ?>

					<option <?php $_el_count = (isset($_sub_val['el'])) ? sizeof($_sub_val['el']) : 0;if ($_el_count) {for ($_el_i = 0; $_el_i < $_el_count; ++$_el_i){$_el_val = &$_sub_val['el'][$_el_i]; ?> <?php echo $_el_val['NAME']; ?>="<?php echo $_el_val['PARAM']; ?>"<?php }} ?>><?php echo $_sub_val['DESCRIPTION']; ?></option>
				<?php }} ?>

				</select>
			</fieldset>
		</div>		
		<?php }} $_radios_count = (isset($this->_tpldata['radios'])) ? sizeof($this->_tpldata['radios']) : 0;if ($_radios_count) {for ($_radios_i = 0; $_radios_i < $_radios_count; ++$_radios_i){$_radios_val = &$this->_tpldata['radios'][$_radios_i]; ?>

		<div class="form_fieldset">
			<fieldset>
				<legend><?php echo $_radios_val['GROUP_NAME']; ?></legend>
				<?php $_sub_count = (isset($_radios_val['sub'])) ? sizeof($_radios_val['sub']) : 0;if ($_sub_count) {for ($_sub_i = 0; $_sub_i < $_sub_count; ++$_sub_i){$_sub_val = &$_radios_val['sub'][$_sub_i]; ?>

					<input <?php $_el_count = (isset($_sub_val['el'])) ? sizeof($_sub_val['el']) : 0;if ($_el_count) {for ($_el_i = 0; $_el_i < $_el_count; ++$_el_i){$_el_val = &$_sub_val['el'][$_el_i]; ?> <?php echo $_el_val['NAME']; ?>="<?php echo $_el_val['PARAM']; ?>"<?php }} ?>> <?php echo $_sub_val['DESCRIPTION']; ?><br>
				<?php }} ?>

			</fieldset>
		</div>		
		<?php }} ?>


        
        <div class="menu-buttons">
		        <input type="submit" name="start_test" value="Начать тест" style="border-radius: 10px; position: relative; left: 15px; top: 15px; width: 145px; font-family: Verdana; height: 35px; color: #1e8fc7;"> &nbsp; 
				<input type="submit" name="show_lesson_contents" value="Показать вопросы" style="border-radius: 10px; position: relative; left: 15px; top: 15px; width: 145px; font-family: Verdana; height: 35px; color: #1e8fc7;">        </div>

        </form>
        
        </div>
        </td>
      </tr>
    </table>
<div style="font-style:italic;font-size:large;text-align:left; padding-top:50px;">Рекомендуем ознакомиться: <a href="http://phpbb.aliyah.co/viewtopic.php?f=6&amp;t=16http://phpbb.aliyah.co/viewtopic.php?f=6&amp;t=16">Инструкция и замечания! </a></div>
    <?php if ($this->_rootref['L_DB_QUERIES']) {  ?>

    <div style="font-style:italic;font-size:x-small;text-align:left;">DB queries: <?php echo ((isset($this->_rootref['L_DB_QUERIES'])) ? $this->_rootref['L_DB_QUERIES'] : ((isset($user->lang['DB_QUERIES'])) ? $user->lang['DB_QUERIES'] : '{ DB_QUERIES }')); ?> <br> DB query time: <?php echo ((isset($this->_rootref['L_DB_QUERY_TIME'])) ? $this->_rootref['L_DB_QUERY_TIME'] : ((isset($user->lang['DB_QUERY_TIME'])) ? $user->lang['DB_QUERY_TIME'] : '{ DB_QUERY_TIME }')); ?></div>
    <?php } ?>

  </div>

</div>