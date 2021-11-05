<?php /* Smarty version 2.6.26, created on 2011-10-31 14:18:02
         compiled from index.html */ ?>
<html>
	<head>
		<title>Server status LIVE :: <?php echo $this->_tpl_vars['PAGE_TITLE']; ?>
</title>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['PATH_TO_TEMPLATE']; ?>
/stylesheet.css" />
	</head>
	<body>
		<div class="wrapper1"><div class="wrapper2"><div class="content">
			<div class="crtl"></div>
			<div class="crtr"><div class="crtrc"></div></div>
			<div class="crbl"><div class="crblc"></div></div>
			<div class="crbr"><div class="crbrc"></div></div>
			<div class="lntc"></div>
			<div class="lnbc"><div class="lnbcc"></div></div>
			<div class="lncl"></div>
			<div class="lncr"><div class="lncrc"></div></div>
				<div class="one_two">
					<div class="form"><form action="<?php echo $this->_tpl_vars['FORM_ACTION']; ?>
" method="<?php echo $this->_tpl_vars['FORM_METHOD']; ?>
"><input name="<?php echo $this->_tpl_vars['HOST_NAME']; ?>
" value="<?php echo $this->_tpl_vars['HOST_VALUE']; ?>
" class="host"/>
					<input name="<?php echo $this->_tpl_vars['PORT_NAME']; ?>
" value="<?php echo $this->_tpl_vars['PORT_VALUE']; ?>
" class="port"/><input type="submit" value="OK" class="submit"/></form></div>
					<?php if ($this->_tpl_vars['SERVER_DOWN'] == false): ?>
						<h1>Server status LIVE</h1>
						<div class="property">Nume:</div>
						<div class="value"><?php echo $this->_tpl_vars['SERVER_HOSTNAME']; ?>
</div>
						<div class="property">Adresa:</div>
						<div class="value"><?php echo $this->_tpl_vars['SERVER_IP']; ?>
</div>
						<div class="property">Mod:</div>
						<div class="value"><?php echo $this->_tpl_vars['SERVER_MOD']; ?>
</div>
						<div class="property">Parola:</div>
						<div class="value"><?php echo $this->_tpl_vars['SERVER_PAROLA']; ?>
</div>
						<div class="property">Jucatori:</div>
						<div class="value"><?php echo $this->_tpl_vars['SERVER_PLAYERS']; ?>
</div>
						<div class="property">Harta:</div>
						<div class="value"><?php echo $this->_tpl_vars['SERVER_MAP']; ?>
</div>
					<?php else: ?>
						<h1>Server is DOWN</h1>
						<?php if ($this->_tpl_vars['STATS_DEBUG']): ?>
							<pre><?php echo $this->_tpl_vars['STATS_ERROR']; ?>
</pre>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<div class="two_two">
					<?php if ($this->_tpl_vars['SERVER_DOWN'] == false): ?>
					<div class="cell3hd"><?php if ($this->_tpl_vars['GAME_TYPE'] == 1): ?>Timp<?php else: ?>Ping<?php endif; ?></div>
					<div class="cell2hd">Scor</div>
					<div class="cell1hd">Nume jucator</div>
					<?php unset($this->_sections['player']);
$this->_sections['player']['name'] = 'player';
$this->_sections['player']['loop'] = is_array($_loop=($this->_tpl_vars['SERVER_PLAYER_STATS'])) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['player']['show'] = true;
$this->_sections['player']['max'] = $this->_sections['player']['loop'];
$this->_sections['player']['step'] = 1;
$this->_sections['player']['start'] = $this->_sections['player']['step'] > 0 ? 0 : $this->_sections['player']['loop']-1;
if ($this->_sections['player']['show']) {
    $this->_sections['player']['total'] = $this->_sections['player']['loop'];
    if ($this->_sections['player']['total'] == 0)
        $this->_sections['player']['show'] = false;
} else
    $this->_sections['player']['total'] = 0;
if ($this->_sections['player']['show']):

            for ($this->_sections['player']['index'] = $this->_sections['player']['start'], $this->_sections['player']['iteration'] = 1;
                 $this->_sections['player']['iteration'] <= $this->_sections['player']['total'];
                 $this->_sections['player']['index'] += $this->_sections['player']['step'], $this->_sections['player']['iteration']++):
$this->_sections['player']['rownum'] = $this->_sections['player']['iteration'];
$this->_sections['player']['index_prev'] = $this->_sections['player']['index'] - $this->_sections['player']['step'];
$this->_sections['player']['index_next'] = $this->_sections['player']['index'] + $this->_sections['player']['step'];
$this->_sections['player']['first']      = ($this->_sections['player']['iteration'] == 1);
$this->_sections['player']['last']       = ($this->_sections['player']['iteration'] == $this->_sections['player']['total']);
?>
					<div class="cell3">
						<?php if ($this->_tpl_vars['GAME_TYPE'] == 1): ?>
							<?php echo $this->_tpl_vars['SERVER_PLAYER_STATS'][$this->_sections['player']['index']]->TimePlayed; ?>

						<?php else: ?>
							<?php echo $this->_tpl_vars['SERVER_PLAYER_STATS'][$this->_sections['player']['index']]->Ping; ?>

						<?php endif; ?>
					</div>
					<div class="cell2"><?php echo $this->_tpl_vars['SERVER_PLAYER_STATS'][$this->_sections['player']['index']]->Score; ?>
</div>
					<div class="cell1"><?php echo $this->_tpl_vars['SERVER_PLAYER_STATS'][$this->_sections['player']['index']]->Name; ?>
</div>
					<?php endfor; endif; ?>
					<?php endif; ?>
				</div>
			<div class="footer">
				Powered by <a href="http://www.freakz.ro/forum">Freakz.Ro</a>.
			</div>
		</div></div></div>
	</body>
</html>