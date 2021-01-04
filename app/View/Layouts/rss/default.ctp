<?php
if (!isset($channel)):
    $channel = [];
endif;
if (!isset($channel['title'])):
    $channel['title'] = $title_for_layout;
endif;

echo $this->Rss->document(
    $this->Rss->channel(
        [], $channel, $this->fetch('content')
    )
);
?>
