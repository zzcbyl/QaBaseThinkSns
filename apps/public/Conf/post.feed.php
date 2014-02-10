<feed app='public' type='post' info='原创提问'>
	<title comment="feed标题"> 
		<![CDATA[{$actor}]]>
	</title>
	<body comment="feed详细内容/引用的内容">
		<![CDATA[{$body|t|replaceUrl} ]]>
	</body>
	<description comment="feed描述内容/引用的描述内容">
		<![CDATA[{$description} ]]>
	</description>
	<feedAttr comment="true" repost="true" like="false" favor="true" delete="true" />
</feed>