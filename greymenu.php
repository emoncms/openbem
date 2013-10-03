<?php global $path; ?>

<style>

.greydashmenu {
  display: block;
  list-style: none outside none;
  margin: 0;
  padding: 0;
}

.greydashmenu li {
  list-style: none outside none;
  margin: 0;
  padding: 0;
  border-right: 1px solid #eee;
  float: left;
}

.greydashmenu li a {
  display: block;
  margin: 0;
  padding: 0 12px;
  border-right: 1px solid #ccc;
  text-decoration: none;
  font: 13px/27px sans-serif;
  text-transform: none;
}

</style>

<span style="float:left; color:#888; font: 13px/27px sans-serif; font-weight:bold; "><?php echo _("Buildings:"); ?></span>

<ul class="greydashmenu">
  <li><a href="<?php echo $path; ?>openbem/monthly/1">1</a></li>
  <li><a href="<?php echo $path; ?>openbem/monthly/2">2</a></li>
  <li><a href="<?php echo $path; ?>openbem/monthly/3">3</a></li>
  <li><a href="<?php echo $path; ?>openbem/monthly/4">4</a></li>
</ul>
