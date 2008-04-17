<?php Typecho::feedHeader('RSS2.0', $options->charset, array('content', 'wfw', 'dc')); ?>
<?php widget('contents.FeedPosts')->to($feeds); ?>

<channel>
<title><?php $options->title(); ?></title>
<link><?php $options->site_url(); ?></link>
<description><?php $options->description(); ?></description>
<language>en</language>
<docs>http://blogs.law.harvard.edu/tech/rss</docs>
<generator><?php $options->generator(); ?></generator>

<?php while($feeds->get()): ?>
<title><?php $feeds->title(); ?></title>
<link><?php $feeds->permalink(); ?></link>
<comments><?php $feeds->permalink('#comments'); ?></comments>
<category><?php $feeds->category('</category><category>', false); ?></category>
<guid isPermaLink="true"><?php $feeds->permalink(); ?></guid>
<author><?php $feeds->author(); ?></author>
<dc:creator><?php $feeds->author(); ?></dc:creator>
<pubDate><?php $feeds->date('r'); ?></pubDate>
<description><![CDATA[<?php $feeds->excerpt(); ?>]]></description>
<content:encoded><![CDATA[<?php $feeds->text(); ?>]]></content:encoded>
<wfw:commentRss><?php $feeds->feedUrl(); ?></wfw:commentRss>
<?php endwhile; ?>

</channel>
</rss>
