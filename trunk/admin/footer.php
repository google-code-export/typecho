	<hr class="space" />
	<div id="footer">
		<img src="images/logo-ghost.png" alt="logo" />&nbsp;&nbsp;<?php _e('基于<a href="http://www.typecho.org">%s</a>构建', $options->generator); ?>.
	</div>
</div>
<script>
    $(document).ajaxStart(
        function(){
            $(".loading").remove();
            loadingImage = new Image();
            loadingImage.src = "<?php $options->adminUrl('/images/loading.gif'); ?>";
            $(loadingImage).addClass("loading");
            $("#main h2").append(loadingImage);
        }
    );
    
    $(document).ajaxStop(
        function(){
            $(".loading").remove();
        }
    );
</script>
</body>
</html>
