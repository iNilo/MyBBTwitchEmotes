<?php
//general
$jsonurl = "https://twitchemotes.com/api_cache/v2/global.json";
$json = file_get_contents($jsonurl);
$data = json_decode($json);

//var_dump($data);

$emotes = $data->emotes;
$template = $data->template;

//var_dump($emotes);

$link = mysqli_connect("localhost", "root", "", "");




foreach ($emotes as  $name => $emote_detail)
{
    $emote_desc = $emote_detail->description;
    $emote_id = $emote_detail->image_id;


    //lets sql it.
    $sql_title = $name;
    $sql_description = $emote_desc;
    $sql_regex = '('.$name.')\\\b';
    $sql_replacement = '<img src="https://static-cdn.jtvnw.net/emoticons/v1/'.$emote_id.'/1.0" alt="'.$name.'"/>';
    $sql_active = true;
    //defaults
    //echo "INSERT INTO `mybb`.`mybb_mycode` (`cid`, `title`, `description`, `regex`, `replacement`, `active`, `parseorder`) VALUES (NULL, '".$sql_title."', '".mysqli_real_escape_string($link,$sql_description)."', '".$sql_regex."', '".$sql_replacement."', '1', '0');\n";
    //subscribers
    //echo "INSERT INTO `mybb`.`mybb_mycode` (`cid`, `title`, `description`, `regex`, `replacement`, `active`, `parseorder`) VALUES (NULL, '".$sql_title."', '".mysqli_real_escape_string($link,$sql_description)."', '".$sql_regex."', '".$sql_replacement."', '1', '0');\n";
    //echo $sql_title. " ";

    $line = "INSERT INTO `mybb`.`mybb_mycode` (`cid`, `title`, `description`, `regex`, `replacement`, `active`, `parseorder`) VALUES (NULL, '".$sql_title."', '".mysqli_real_escape_string($link,$sql_description)."', '".$sql_regex."', '".$sql_replacement."', '1', '0');\n";

    file_put_contents("twitch_general_emotes.sql", $line, FILE_APPEND);

}




//subscriber emotes
$channel_data = json_decode(file_get_contents("http://twitchemotes.com/api_cache/v2/subscriber.json"));


$count = 0;
foreach ($channel_data->channels as $channel)
{
    foreach ($channel->emotes as $emote)
    {

        //var_dump($emote);
        $sql_title = $emote->code;
        $sql_description = $emote->code;
        $sql_regex = ':'.$emote->code.':';
        $sql_replacement = '<img src="https://static-cdn.jtvnw.net/emoticons/v1/'.$emote->image_id.'/1.0" alt="'.$emote->code.'"/>';
        $sql_active = true;
        //defaults
        //echo "INSERT INTO
        //echo ":". $sql_title. ": ";
        //echo "INSERT INTO `mybb`.`mybb_mycode` (`cid`, `title`, `description`, `regex`, `replacement`, `active`, `parseorder`) VALUES (NULL, '".$sql_title."', '".mysqli_real_escape_string($link,$sql_description)."', '".$sql_regex."', '".$sql_replacement."', '1', '0');\n";

        $line = "INSERT INTO `mybb`.`mybb_mycode` (`cid`, `title`, `description`, `regex`, `replacement`, `active`, `parseorder`) VALUES (NULL, '".$sql_title."', '".mysqli_real_escape_string($link,$sql_description)."', '".$sql_regex."', '".$sql_replacement."', '1', '0');\n";


        $file_title = "twitch_channels_". round($count/5000) . ".sql";
        $count++;
        echo "\nTotal emotes ".$count;
        file_put_contents($file_title, $line, FILE_APPEND);


    }


}
