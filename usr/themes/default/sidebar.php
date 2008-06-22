

    <div class="grid_5" id="sidebar">
		<div class="widget">
            <h2>Recent Articles</h2>
            <ul>
                <?php $this->widget('Contents/Post/Recent')
                ->parse('<li><a href="{permalink}">{title}</a></li>'); ?>
            </ul>
        </div>
		<div class="widget">
            <h2>Recent Comments</h2>
            <ul>
                <li>sluke: <a href="#">@insraq新版在秘密开发中...</a></li>
                <li>Jeffrey04: <a href="#">frameworks最基本上要解决的...</a></li>
                <li>insraq: <a href="#">分析精当。另外，Magike最近...</a></li>
                <li>aw: <a href="#">结构在很大程度上说明了思考...</a></li>
                <li>humeniuc: <a href="#">一个人住的My饥渴同学~~~</a></li>
                <li>SACN: <a href="#">打少了点字上面的某句是：与...</a></li>
            </ul>
        </div>
        <div class="widget">
            <h2>Categories</h2>
            <ul>
                <?php $this->widget('Metas/Category/List')
                ->parse('<li><a href="{permalink}">{name}</a> ({count})</li>'); ?>
            </ul>
        </div>
        <div class="widget">
            <h2>Archives</h2>
            <ul>
                <li><a href="#">September 2008</a></li>
                <li><a href="#">October 2008</a></li>
            </ul>
        </div>
		<div class="widget">
			<h2>Blogroll</h2>
			<ul>
				<li><a href="http://ooboy.net/blog/">Fdream</a></li>
				<li><a href="http://hee.ajaxeye.com">elliott</a></li>
				<li><a href="http://wi2ard.com/blog">wi2ard</a></li>
				<li><a href="http://www.monsternunu.com">monsternunu</a></li>
				<li><a href="http://www.hi-open.cn/">Feeling</a></li>
				<li><a href="http://www.magike.net">Magike</a></li>
				<li><a href="http://www.ytblog.com/">Mouse</a></li>
				<li><a href="http://www.ccvita.com">ccvita</a></li>
				<li><a href="http://www.vichair.cn">Vichair</a></li>
				<li><a href="http://www.luweiqing.com">sluke</a></li>
				<li><a href="http://www.coolcode.cn">coolcode</a></li>
				<li><a href="http://aileenguan.cn">Aileenguan</a></li>
				<li><a href="http://www.gracecode.com">Gracecode</a></li>
			</ul>
		</div>
		<div class="widget">
            <h2>Meta</h2>
            <ul>
                <li><a href="#">Login</a></li>
                <li><a href="#">Valid XHTML</a></li>
                <li><a href="#">Typecho</a></li>
            </ul>
        </div>
    </div><!-- end #sidebar -->
