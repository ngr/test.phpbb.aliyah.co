<?php if (!defined('IN_PHPBB')) exit; ?>Subject: Просьба о вступлении в группу

Уважаемый(ая) <?php echo (isset($this->_rootref['USERNAME'])) ? $this->_rootref['USERNAME'] : ''; ?>!

Пользователь «<?php echo (isset($this->_rootref['REQUEST_USERNAME'])) ? $this->_rootref['REQUEST_USERNAME'] : ''; ?>» попросил о вступлении в группу «<?php echo (isset($this->_rootref['GROUP_NAME'])) ? $this->_rootref['GROUP_NAME'] : ''; ?>», лидером которой вы являетесь на конференции «<?php echo (isset($this->_rootref['SITENAME'])) ? $this->_rootref['SITENAME'] : ''; ?>» .
Чтобы удовлетворить или отклонить эту просьбу, перейдите по следующей ссылке:

<?php echo (isset($this->_rootref['U_PENDING'])) ? $this->_rootref['U_PENDING'] : ''; ?>


<?php echo (isset($this->_rootref['EMAIL_SIG'])) ? $this->_rootref['EMAIL_SIG'] : ''; ?>