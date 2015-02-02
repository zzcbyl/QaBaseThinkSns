<feed app='public' type='postfile' info='发附件提问'>
    <title>
        <![CDATA[{$actor}]]>
    </title>
    <body>
    <![CDATA[
    {$body|t|replaceUrl|stripslashes}

    ]]>
    </body>
    <feedAttr comment="true" repost="true" like="false" favor="true" delete="true" />
</feed>