<?php
//configuration here:
//the table name of mybb
$mybb_table_name = "mybb";
//what you want to use for subscriber trigger (default is colon)
$subscriber_activator = ":";

Echo "First off, lets delete all the old files.\n";
$twitch_full_file = __DIR__ . DIRECTORY_SEPARATOR .'ExportedSQL' . DIRECTORY_SEPARATOR . 'twitch_all_emotes.sql';
$twitch_general_files = __DIR__ . DIRECTORY_SEPARATOR .'ExportedSQL' . DIRECTORY_SEPARATOR . 'twitch_general_emotes.sql';
$twitch_subscriber_file = __DIR__ . DIRECTORY_SEPARATOR .'ExportedSQL' . DIRECTORY_SEPARATOR . 'twitch_subscriber_emotes.sql';
unlink($twitch_full_file);
unlink($twitch_general_files);
unlink($twitch_subscriber_file);
Echo "Connecting to local database since im too lazy to figure out how to escape without a link\n";
$link = mysqli_connect("localhost", "root", "", "");
Echo "Grabbing latest data from twitchemotes.com (thanks!)\n";
$global_data = json_decode(file_get_contents("http://twitchemotes.com/api_cache/v2/global.json"));
if($global_data == null)
{
    echo "API is dead?";
    exit();
}
$general_count = 0;
$lines = ""; //clear it
foreach ($global_data->emotes as $name => $emote_detail)
{
    $emote_desc = $emote_detail->description;
    $emote_id = $emote_detail->image_id;
    //lets sql it.
    $sql_title = $name;
    $sql_description = $emote_desc;
    $sql_regex = '('.$name.')\\\b';
    $sql_replacement = '<img src="https://static-cdn.jtvnw.net/emoticons/v1/'.$emote_id.'/1.0" alt="'.$name.'"/>';
    $sql_active = true;
    $lines .= "INSERT INTO `".$mybb_table_name."`.`mybb_mycode` (`cid`, `title`, `description`, `regex`, `replacement`, `active`, `parseorder`) VALUES (NULL, '".$sql_title."', '".mysqli_real_escape_string($link,$sql_description)."', '".$sql_regex."', '".$sql_replacement."', '1', '0');\n";
    $general_count++;
}   
//poop it out into a file
file_put_contents($twitch_full_file, $lines, FILE_APPEND);
file_put_contents($twitch_general_files, $lines, FILE_APPEND);

echo "Generated SQL for ".$general_count ." emotes. \n";
echo "Going to do all the subscriber emotes now... fuckton of emotes, be ready to wait a bit. \n";
//subscriber emotes
$channel_data = json_decode(file_get_contents("http://twitchemotes.com/api_cache/v2/subscriber.json"));
if($global_data == null)
{
    echo "API is dead?";
    exit();
}
$subscriber_count = 0;
$lines = ""; //clear it 
foreach ($channel_data->channels as $channel)
{
    foreach ($channel->emotes as $emote)
    {
        $sql_title = $emote->code;
        $sql_description = $emote->code;
        $sql_regex = $subscriber_activator.$emote->code.$subscriber_activator;
        $sql_replacement = '<img src="https://static-cdn.jtvnw.net/emoticons/v1/'.$emote->image_id.'/1.0" alt="'.$emote->code.'"/>';
        $sql_active = true;
        $lines .= "INSERT INTO `".$mybb_table_name."`.`mybb_mycode` (`cid`, `title`, `description`, `regex`, `replacement`, `active`, `parseorder`) VALUES (NULL, '".$sql_title."', '".mysqli_real_escape_string($link,$sql_description)."', '".$sql_regex."', '".$sql_replacement."', '1', '0');\n";
        $subscriber_count++;
    }

}
//write it to the files.
file_put_contents($twitch_full_file, $lines, FILE_APPEND); //write to global file.
file_put_contents($twitch_subscriber_file, $lines, FILE_APPEND); //write to global file.

echo "Generated SQL for ".$subscriber_count ." subscriber emotes.\n";
