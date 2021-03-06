<?php
/**
 * @package Twig
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */
?>
<form method="post" class="form-horizontal">
  <input type="hidden" name="token" value="<?php echo $_token; ?>">
  <div class="form-group">
    <label class="col-md-2 control-label"><?php echo $this->text('Debug mode'); ?></label>
    <div class="col-md-4">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="settings[debug]" value="1"<?php echo $settings['debug'] ? ' checked' : ''; ?>> <?php echo $this->text('Enabled'); ?>
        </label>
      </div>
      <div class="help-block">
        <div class="text-muted">
          <?php echo $this->text('If enabled, then you can use native Twig debugger to see available variables in templates <code>{{ dump }}</code>'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-2 control-label"><?php echo $this->text('Auto reload'); ?></label>
    <div class="col-md-4">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="settings[auto_reload]" value="1"<?php echo $settings['auto_reload'] ? ' checked' : ''; ?>> <?php echo $this->text('Enabled'); ?>
        </label>
      </div>
      <div class="help-block">
        <div class="text-muted">
          <?php echo $this->text('Recompile Twig template whenever the source code changes. Should be disabled in production!'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-2 control-label"><?php echo $this->text('Strict variables'); ?></label>
    <div class="col-md-4">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="settings[strict_variables]" value="1"<?php echo $settings['strict_variables'] ? ' checked' : ''; ?>> <?php echo $this->text('Enabled'); ?>
        </label>
      </div>
      <div class="help-block">
        <div class="text-muted">
          <?php echo $this->text('If enabled, then Twig will throw exceptions on invalid variables. Should be disabled in production!'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-2 control-label"><?php echo $this->text('Cache'); ?></label>
    <div class="col-md-4">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="settings[cache]" value="1"<?php echo $settings['cache'] ? ' checked' : ''; ?>> <?php echo $this->text('Enabled'); ?>
        </label>
      </div>
      <div class="help-block">
        <div class="text-muted">
          <?php echo $this->text('Cache compiled templates to improve performance. Should be enabled in production'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-4 col-md-offset-2">
      <a href="<?php echo $this->url("admin/module/list"); ?>" class="btn btn-default"><?php echo $this->text("Cancel"); ?></a>
      <button class="btn btn-default save" name="save" value="1"><?php echo $this->text("Save"); ?></button>
    </div>
  </div>
</form>