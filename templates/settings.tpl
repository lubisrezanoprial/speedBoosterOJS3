<script>
	$(function() {ldelim}
		$('#speedBoosterSettings').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="speedBoosterSettings" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
	{csrf}
	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="speedBoosterSettingsFormNotification"}

	<div id="description">
		<p>{translate key="plugins.generic.speedBooster.description"}</p>
		<p style="background: #fffbcc; padding: 10px; border-left: 3px solid #ffeb3b;">
			<strong>⚠️ Catatan Penting:</strong><br>
			Plugin ini aman digunakan. Jika terjadi masalah, cukup nonaktifkan plugin dan semua akan kembali normal.
			Pengaturan tidak akan mempengaruhi halaman admin.
		</p>
	</div>

	{fbvFormArea id="speedBoosterSettingsFormArea"}
		{fbvFormSection}
			{fbvElement type="checkbox" id="minifyHtml" name="minifyHtml" value="1" checked=$minifyHtml label="plugins.generic.speedBooster.settings.minifyHtml" translate=true}
			<p class="description">{translate key="plugins.generic.speedBooster.settings.minifyHtml.description"}</p>
		{/fbvFormSection}

		{fbvFormSection}
			{fbvElement type="checkbox" id="minifyCss" name="minifyCss" value="1" checked=$minifyCss label="plugins.generic.speedBooster.settings.minifyCss" translate=true}
			<p class="description">{translate key="plugins.generic.speedBooster.settings.minifyCss.description"}</p>
		{/fbvFormSection}

		{fbvFormSection}
			{fbvElement type="checkbox" id="minifyJs" name="minifyJs" value="1" checked=$minifyJs label="plugins.generic.speedBooster.settings.minifyJs" translate=true}
			<p class="description">{translate key="plugins.generic.speedBooster.settings.minifyJs.description"}</p>
		{/fbvFormSection}
	{/fbvFormArea}

	{fbvFormButtons}
</form>

<style>
.description {
	font-size: 0.9em;
	color: #666;
	margin-top: 5px;
	font-style: italic;
}
</style>