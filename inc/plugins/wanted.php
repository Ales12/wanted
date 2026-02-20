<?php

// Disallow direct access to this file for security reasons
if (!defined("IN_MYBB")) {
    die("Direct initialization of this file is not allowed.");
}
// Hooks
// ADMIN-CP PEEKER
$plugins->add_hook('admin_config_settings_change', 'wanted_settings_change');
$plugins->add_hook('admin_settings_print_peekers', 'wanted_settings_peek');

//Newthread Hooks
$plugins->add_hook("newthread_start", "wanted_newscene");
$plugins->add_hook("newthread_do_newthread_end", "wanted_newscene_do");

// Thread editieren
$plugins->add_hook("editpost_end", "wanted_editscene");
$plugins->add_hook("editpost_do_editpost_end", "wanted_editscene_do");

// Showthread
$plugins->add_hook("showthread_start", "wanted_showthread");

// forumdisplay
$plugins->add_hook("forumdisplay_thread_end", "wanted_forumdisplay");

// global
$plugins->add_hook("global_intermediate", "wanted_global");

// forumbits
$plugins->add_hook("build_forumbits_forum", "wanted_build_forumbits_forum");

// profile
$plugins->add_hook("member_profile_end", "wanted_profile");

function wanted_info()
{
    return array(
        "name" => "Gesuchsverwaltung",
        "description" => "Hier kannst du deinen Gesuchen weitere Informationen hinzufügen",
        "website" => "",
        "author" => "Ales",
        "authorsite" => "",
        "version" => "1.0",
        "guid" => "",
        "codename" => "",
        "compatibility" => "*"
    );
}

function wanted_install()
{
    global $db, $mybb, $cache;

    $db->query("ALTER TABLE `" . TABLE_PREFIX . "threads` ADD `relation` varchar(500) CHARACTER SET utf8 NOT NULL;");
    $db->query("ALTER TABLE `" . TABLE_PREFIX . "threads` ADD `age` varchar(500) CHARACTER SET utf8 NOT NULL;");
    $db->query("ALTER TABLE `" . TABLE_PREFIX . "threads` ADD `job` varchar(500) CHARACTER SET utf8 NOT NULL;");
    $db->query("ALTER TABLE `" . TABLE_PREFIX . "threads` ADD `info` varchar(500) CHARACTER SET utf8 NOT NULL;");
    $db->query("ALTER TABLE `" . TABLE_PREFIX . "threads` ADD `avatarperson` varchar(500) CHARACTER SET utf8 NOT NULL;");

    $setting_group = array(
        'name' => 'wanted',
        'title' => 'Einstellung für die Gesuchsverwaltung',
        'description' => 'Hier kannst du die Einstellungen für deine Gesuchsverwaltung einfügen.',
        'disporder' => 5, // The order your setting group will display
        'isdefault' => 0
    );

    $gid = $db->insert_query("settinggroups", $setting_group);

    $setting_array = array(
        'wanted_foren' => array(
            'title' => 'Foren für Headerbereich',
            'description' => 'Wähle hier die Foren, die im Headerbereich ausgelesen werden sollen:',
            'optionscode' => "forumselect",
            'value' => '13,14',
            'disporder' => 1
        ),
        // A select box
        'wanted_profile' => array(
            'title' => 'Gesuche im Profil',
            'description' => 'Sollen Gesuche im Profil angezeigt werden?',
            'optionscode' => "yesno",
            'value' => 1,
            'disporder' => 2
        ),

        // A select box
        'wanted_above_cat' => array(
            'title' => 'Über eine Kategorie anzeigen',
            'description' => 'Sollen Gesuche über der Gesuchskategorie angezeigt werden?',
            'optionscode' => "yesno",
            'value' => 1,
            'disporder' => 3
        ),
        'wanted_cat' => array(
            'title' => 'Kategorie wählen',
            'description' => 'Über welcher Kategorie soll es angezeigt werden?',
            'optionscode' => "forumselectsingle",
            'value' => '1',
            'disporder' => 4
        ),
    );

    foreach ($setting_array as $name => $setting) {
        $setting['name'] = $name;
        $setting['gid'] = $gid;

        $db->insert_query('settings', $setting);
    }

    // templates
    
    $insert_array = array(
        'title' => 'wanted_bit_global',
        'template' => $db->escape_string('<div>
	<div class="tcat"><strong>{$wanted_title}</strong></div>
	{$random_wanted}
</div>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);
	
    $insert_array = array(
        'title' => 'wanted_forumdisplay',
        'template' => $db->escape_string('<div class="wanted_forumdisplay">
<div>{$lang->wanted_fd_rela} {$thread[\'relation\']}</div>
<div>{$lang->wanted_fd_age} {$thread[\'age\']}</div>
<div>{$lang->wanted_fd_job} {$thread[\'job\']}</div>
<div>{$lang->wanted_fd_info} {$thread[\'info\']}</div>
<div>{$lang->wanted_fd_avatar} {$thread[\'avatarperson\']}</div>
</div>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);
	
    $insert_array = array(
        'title' => 'wanted_global',
        'template' => $db->escape_string('<table  border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" width="50%" style="margin: 10px auto;">
	<tr>
		<td class="thead"><strong>{$lang->wanted_global_title}</strong></td>
	</tr>
	<tr>
		<td class="trow1"><div class="wanted_global_flex">
			{$wanted_bit}
			</div></td>
	</tr>
</table>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);
	
    $insert_array = array(
        'title' => 'wanted_global_bit',
        'template' => $db->escape_string('<div class="wanted_headerlink">{$wantedlink}</div>
<div class="wanted_seeker">{$seeked_by}</div>
<div class="wanted_infos">{$lang->wanted_global_rela} {$relation}</div>
<div class="wanted_infos">{$lang->wanted_global_age} {$age}</div>
<div class="wanted_infos">{$lang->wanted_global_job} {$job}</div>
<div class="wanted_infos">{$lang->wanted_global_info} {$info}</div>
<div class="wanted_infos">{$lang->wanted_global_avatar} {$avatar}</div>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);
	
    $insert_array = array(
        'title' => 'wanted_infos',
        'template' => $db->escape_string('<tr>
<td class="trow2" width="20%"><strong>{$lang->wanted_nt_rela}</strong>
	<div class="smalltext">{$lang->wanted_nt_rela_desc}</div></td>
<td class="trow2"><input type="text" class="textbox" name="relation" size="40" maxlength="85" value="{$relation}" tabindex="1" /></td>
</tr>
<tr>
<td class="trow2" width="20%"><strong>{$lang->wanted_nt_age}</strong>
	<div class="smalltext">{$lang->wanted_nt_age_desc}</div></td>
<td class="trow2"><input type="text" class="textbox" name="age" size="40" maxlength="85" value="{$age}" tabindex="1" /></td>
</tr>
<tr>
<td class="trow2" width="20%"><strong>{$lang->wanted_nt_job}</strong>
	<div class="smalltext">{$lang->wanted_nt_job_desc}</div></td>
<td class="trow2"><input type="text" class="textbox" name="job" size="40" maxlength="85" value="{$job}" tabindex="1" /></td>
</tr>
<tr>
<td class="trow2" width="20%"><strong>{$lang->wanted_nt_info}</strong>
	<div class="smalltext">{$lang->wanted_nt_info_desc}</div></td>
<td class="trow2"><input type="text" class="textbox" name="info" size="40" maxlength="85" value="{$info}" tabindex="1" /></td>
</tr>
<tr>
<td class="trow2" width="20%"><strong>{$lang->wanted_nt_avatar}</strong>
	<div class="smalltext">{$lang->wanted_nt_avatar_desc}</div></td>
<td class="trow2"><input type="text" class="textbox" name="avatarperson" size="40" maxlength="85" value="{$avatarperson}" tabindex="1" /></td>
</tr>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);
	
    $insert_array = array(
        'title' => 'wanted_profile',
        'template' => $db->escape_string('<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" style="margin: 2px auto; width: 50%;">
	<tr>
		<td class="thead"><strong>{$lang->wanted_iswanted}</strong></td>
	</tr>
	<tr>
		<td class="trow1">
			{$wanted_profile_bit}
		</td>
	</tr>
</table>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);
	
    $insert_array = array(
        'title' => 'wanted_profile_bit',
        'template' => $db->escape_string('<div class="wanted_profilelink">{$wantedlink}</div>
<div class="wanted_profile_infos">{$lang->wanted_profile_rela} {$relation}</div>
<div class="wanted_profile_infos">{$lang->wanted_profile_age} {$age}</div>
<div class="wanted_profile_infos">{$lang->wanted_profile_job} {$job}</div>
<div class="wanted_profile_infos">{$lang->wanted_profile_info} {$info}</div>
<div class="wanted_profile_infos">{$lang->wanted_profile_avatar} {$avatar}</div>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);
	
    $insert_array = array(
        'title' => 'wanted_showthread',
        'template' => $db->escape_string('<tr>
	<td class="trow1">
		<div class="wanted_flex">
			<div>
			{$relation}
			<div class="wanted_infopoint">{$lang->wanted_st_rela}</div>
			</div>
			<div>
			{$age}
			<div class="wanted_infopoint">{$lang->wanted_st_age}</div>
			</div>
			<div>
			{$job}
			<div class="wanted_infopoint">{$lang->wanted_st_job}</div>
			</div>
			<div>
			{$info}
			<div class="wanted_infopoint">{$lang->wanted_st_info}</div>
			</div>
			<div>
			{$avatar}
			<div class="wanted_infopoint">{$lang->wanted_st_avatar}</div>
			</div>
		</div>
	</td>
</tr>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);
	

        // CSS 
    //CSS einfügen
    $css = array(
        'name' => 'wanted.css',
        'tid' => 1,
        'attachedto' => '',
        "stylesheet" => '/**Showthread**/
.wanted_flex{
	display: flex;
	justify-content: space-evenly;
	gap: 3px 5px;
}

.wanted_flex > div{
	padding: 5px;
	width: 25%;
	box-sizing: border-box;
	text-align: center;
		font-size: 15px;
}

.wanted_infopoint{
	font-size: 10px;
	text-transform: uppercase;
	font-weight: bold;
}

/**forumdisplay**/

.wanted_forumdisplay{
	font-size: 10px;	
}

/**global**/
.wanted_global_flex{
	display: flex;
	gap: 2px;
}

.wanted_global_flex > div{
	width: 50%;	
}

.wanted_headerlink{
	font-size: 15px;
	text-align: center;
}

.wanted_seeker{
			font-size: 11px;
	text-align: center;
	text-transform: uppercase;
}

.wanted_infos{
	font-size: 12px;
}

/*profile**/
.wanted_profilelink{
		font-size: 15px;
	text-align: center;
}

.wanted_profile_infos{
	font-size: 12px;
}
',
        'cachefile' => $db->escape_string(str_replace('/', '', 'wanted.css')),
        'lastmodified' => time()
    );

    require_once MYBB_ADMIN_DIR . "inc/functions_themes.php";

    $sid = $db->insert_query("themestylesheets", $css);
    $db->update_query("themestylesheets", array("cachefile" => "css.php?stylesheet=" . $sid), "sid = '" . $sid . "'", 1);

    $tids = $db->simple_select("themes", "tid");
    while ($theme = $db->fetch_array($tids)) {
        update_theme_stylesheet_list($theme['tid']);
    }
    // Don't forget this!
    rebuild_settings();

}

function wanted_is_installed()
{

    global $db;
    if ($db->field_exists("age", "threads")) {
        return true;
    }
    return false;

}

function wanted_uninstall()
{
    global $db;
    if ($db->field_exists("relation", "threads")) {
        $db->drop_column("threads", "relation");
    }
    if ($db->field_exists("age", "threads")) {
        $db->drop_column("threads", "age");
    }
    if ($db->field_exists("job", "threads")) {
        $db->drop_column("threads", "job");
    }
    if ($db->field_exists("info", "threads")) {
        $db->drop_column("threads", "info");
    }
    if ($db->field_exists("avatarperson", "threads")) {
        $db->drop_column("threads", "avatarperson");
    }
    $db->delete_query('settings', "name IN ('wanted_profile', 'wanted_foren', 'wanted_above_cat', 'wanted_cat')");
    $db->delete_query('settinggroups', "name = 'wanted'");
    require_once MYBB_ADMIN_DIR . "inc/functions_themes.php";
    $db->delete_query("themestylesheets", "name = 'wanted.css'");
    $query = $db->simple_select("themes", "tid");
    while ($theme = $db->fetch_array($query)) {
        update_theme_stylesheet_list($theme['tid']);
    }
    $db->delete_query("templates", "title LIKE '%wanted%'");
    // Don't forget this
    rebuild_settings();
}
function wanted_activate()
{
    global $db, $cache;
    require MYBB_ROOT . "/inc/adminfunctions_templates.php";
    find_replace_templatesets("forumbit_depth1_cat", "#" . preg_quote('<table border="0"') . "#i", '{$forum[\'wanted\']}<table border="0"');
    find_replace_templatesets("forumdisplay_thread", "#" . preg_quote('<span class="thread_start_datetime smalltext">{$thread[\'start_datetime\']}</span></div>
		</div>') . "#i", '<span class="thread_start_datetime smalltext">{$thread[\'start_datetime\']}</span></div>
		</div>{$wanted_forumdisplay}');
        find_replace_templatesets("header", "#" . preg_quote('<navigation>') . "#i", '<navigation>   {$wanted_global}');
        find_replace_templatesets("member_profile", "#" . preg_quote('{$awaybit}') . "#i", '{$awaybit} {$wanted_profile}');
	 find_replace_templatesets("showthread", "#" . preg_quote(' <tr><td id="posts_container">') . "#i", '{$wanted_showthread} <tr><td id="posts_container">');
    }

function wanted_deactivate()
{
    require MYBB_ROOT . "/inc/adminfunctions_templates.php";
    find_replace_templatesets("forumbit_depth1_cat", "#" . preg_quote('{$forum[\'wanted\']}') . "#i", '', 0);
    find_replace_templatesets("forumdisplay_thread", "#" . preg_quote('{$wanted_forumdisplay}') . "#i", '', 0);
    find_replace_templatesets("header", "#" . preg_quote('{$wanted_global}') . "#i", '', 0);
    find_replace_templatesets("member_profile", "#" . preg_quote('{$wanted_profile}') . "#i", '', 0);
	  find_replace_templatesets("member_profile", "#" . preg_quote('{$wanted_showthread}') . "#i", '', 0);
}

function wanted_settings_change()
{
    global $db, $mybb, $wanted_settings_peeker;

    $result = $db->simple_select('settinggroups', 'gid', "name='wanted'", array("limit" => 1));
    $group = $db->fetch_array($result);
    if (isset($mybb->input['gid'])) {
        $wanted_settings_peeker = ($mybb->input['gid'] == $group['gid']) && ($mybb->request_method != 'post');
    }
}
function wanted_settings_peek(&$peekers)
{
    global $mybb, $wanted_settings_peeker;

    if ($wanted_settings_peeker) {
        $peekers[] = 'new Peeker($(".setting_wanted_above_cat"), $("#row_setting_wanted_cat"),/1/,true)';

    }
}

/* Newthread Informationen 
 * Hier geht es erstmal darum die Daten in New Thread zu übergeben. Hier wird das Template dafür ausgelesen und es sorgt dafür, dass die Daten nicht verschwinden, wenn man mal auf Vorschau geht.
 */

function wanted_newscene()
{
    global $mybb, $forum, $templates, $lang, $rela, $age, $job, $info, $wanted_newthread, $avatarperson, $post_errors, $thread;
    //Die Sprachdatei
    $lang->load('wanted');
    // variabeln leeren
    $wanted_foren = "";
    // variabel füllen
    $wanted_foren = $mybb->settings['wanted_foren'];
    $all_wanted_foren = explode(",", $wanted_foren);

    foreach ($all_wanted_foren as $wanted_forum) {
        if ($forum['fid'] == $wanted_forum) {
            if ($mybb->input['previewpost'] || $post_errors) {
                $relation = htmlspecialchars($mybb->input['relation']);
                $age = htmlspecialchars($mybb->input['age']);
                $job = htmlspecialchars($mybb->get_input('job'));
                $info = htmlspecialchars($mybb->get_input('info'));
                $avatarperson = htmlspecialchars($mybb->get_input('avatarperson'));
            } else {
                $relation = htmlspecialchars($thread['relation']);
                $age = htmlspecialchars($thread['age']);
                $job = htmlspecialchars($thread['job']);
                $info = htmlspecialchars($thread['info']);
                $avatarperson = htmlspecialchars($thread['avatarperson']);
            }
            eval ("\$wanted_newthread = \"" . $templates->get("wanted_infos") . "\";");
        }
    }
}


/*
 * Dann wollen wir doch alles mal in die Datenbank speichern und unsere Szenenpartner darüber informieren, dass wir eine neue Szene mit ihnen eröffnet haben.
 * und es werden auch noch Alerts ausgelöst. Wie schön.
 */

function wanted_newscene_do()
{
    global $db, $mybb, $templates, $tid, $forum;

    // variabeln leeren
    $wanted_foren = "";
    // variabel füllen
    $wanted_foren = $mybb->settings['wanted_foren'];
    $all_wanted_foren = explode(",", $wanted_foren);

    foreach ($all_wanted_foren as $wanted_forum) {
        if ($forum['fid'] == $wanted_forum) {
            $wantedinfos = array(
                "relation" => $db->escape_string($_POST['relation']),
                "age" => $db->escape_string($_POST['age']),
                "job" => $db->escape_string($_POST['job']),
                "info" => $db->escape_string($_POST['info']),
                "avatarperson" => $db->escape_string($_POST['avatarperson']),
            );

            $db->update_query("threads", $wantedinfos, "tid = {$tid}");
        }
    }
}

/* Wir möchten die Szene auch editieren */
function wanted_editscene()
{
    global $mybb, $db, $templates, $forum, $thread, $post_errors, $lang, $wanted_edit, $scenestatus;
    $lang->load('wanted');

    // variabeln leeren
    $wanted_foren = "";
    // variabel füllen
    $wanted_foren = $mybb->settings['wanted_foren'];
    $all_wanted_foren = explode(",", $wanted_foren);

    foreach ($all_wanted_foren as $wanted_forum) {
        if ($thread['fid'] == $wanted_forum) {
            $pid = $mybb->get_input('pid', MyBB::INPUT_INT);
            if ($thread['firstpost'] == $pid) {
                if ($mybb->input['previewpost'] || $post_errors) {
                    $relation = htmlspecialchars($mybb->input['relation']);
                    $age = htmlspecialchars($mybb->input['age']);
                    $job = htmlspecialchars($mybb->get_input('job'));
                    $info = htmlspecialchars($mybb->get_input('info'));
                    $avatarperson = htmlspecialchars($mybb->get_input('avatarperson'));
                } else {
                    $age = htmlspecialchars($thread['age']);
                    $relation = htmlspecialchars($thread['relation']);
                    $job = htmlspecialchars($thread['job']);
                    $info = htmlspecialchars($thread['info']);
                    $avatarperson = htmlspecialchars($thread['avatarperson']);
                }


                eval ("\$wanted_edit = \"" . $templates->get("wanted_infos") . "\";");

            }
        }

    }
}

/* geänderte Daten  in die Datenbank überspielen*/
function wanted_editscene_do()
{
    global $mybb, $forum, $db, $templates, $thread, $tid;
    // variabeln leeren
    $wanted_foren = "";
    // variabel füllen
    $wanted_foren = $mybb->settings['wanted_foren'];
    $all_wanted_foren = explode(",", $wanted_foren);

    foreach ($all_wanted_foren as $wanted_forum) {
        if ($thread['fid'] == $wanted_forum) {

            $wantedinfos = array(
                "relation" => $db->escape_string($mybb->input['relation']),
                "age" => $db->escape_string($mybb->input['age']),
                "job" => $db->escape_string($mybb->input['job']),
                "info" => $db->escape_string($mybb->input['info']),
                "avatarperson" => $db->escape_string($mybb->input['avatarperson']),
            );
            $db->update_query("threads", $wantedinfos, "tid = {$tid}");
        }
    }
}

function wanted_showthread()
{
    global $thread, $db, $mybb, $forum, $templates, $lang, $wanted_showthread, $relation, $age, $job, $info, $avatar;
    $lang->load('wanted');
    // variabeln leeren
    $wanted_foren = "";
    // variabel füllen
    $wanted_foren = $mybb->settings['wanted_foren'];
    $all_wanted_foren = explode(",", $wanted_foren);

    foreach ($all_wanted_foren as $wanted_forum) {
        if ($forum['fid'] == $wanted_forum) {
            $relation = "";
            $age = "";
            $job = "";
            $info = "";
            $avatar = "";

            // variabeln füllen
            $relation = $thread['relation'];
            $age = $thread['age'];
            $job = $thread['job'];
            $info = $thread['info'];
            $avatar = $thread['avatarperson'];

            eval ("\$wanted_showthread = \"" . $templates->get("wanted_showthread") . "\";");

        }
    }
}

function wanted_forumdisplay(&$thread)
{
    global $db, $mybb, $templates, $thread, $foruminfo, $lang, $wanted_forumdisplay;
    $lang->load('wanted');
    // variabeln leeren
    $wanted_foren = "";
    // variabel füllen
    $wanted_foren = $mybb->settings['wanted_foren'];
    $all_wanted_foren = explode(",", $wanted_foren);

    foreach ($all_wanted_foren as $wanted_forum) {
        if ($foruminfo['fid'] == $wanted_forum) {
            eval ("\$wanted_forumdisplay = \"" . $templates->get("wanted_forumdisplay") . "\";");
        }
    }
}

function wanted_global()
{
    global $db, $mybb, $templates, $lang, $wanted_global, $random_wanted, $wantedlink, $wanted_index;
    $lang->load('wanted');
    // variabeln leeren
    $wanted_foren = "";
    // variabel füllen
    $wanted_foren = $mybb->settings['wanted_foren'];
     $show_cat = $mybb->settings['wanted_above_cat'];

    $all_wanted_foren = explode(",", $wanted_foren);

    foreach ($all_wanted_foren as $wanted_forum) {
        $wanted_title = $db->fetch_field($db->simple_select("forums", "name", "fid = {$wanted_forum}"), "name");

        $random_wanted = "";
        $query = $db->query("SELECT *
        FROM " . TABLE_PREFIX . "threads
        where fid = '{$wanted_forum}'
        and visible = 1
        ORDER BY RAND() LIMIT 1
        ");

        while ($row = $db->fetch_array($query)) {
            $wantedlink = "";
            $relation = "";
            $age = "";
            $job = "";
            $info = "";
            $avatar = "";
            $seeker = "";
            $seeked_by = "";

            $get_user = get_user($row['uid']);

            $wantedlink = "<a href='showthread.php?tid={$row['tid']}'>{$row['subject']}</a>";
            $username = format_name($get_user['username'], $get_user['usergroup'], $get_user['displaygroup']);
            $seeker = build_profile_link($username, $get_user['uid']);
            $seeked_by = $lang->sprintf($lang->wanted_global_seeker, $seeker);
            $relation = $row['relation'];
            $age = $row['age'];
            $job = $row['job'];
            $info = $row['info'];
            $avatar = $row['avatarperson'];
            eval ("\$random_wanted .= \"" . $templates->get("wanted_global_bit") . "\";");
        }
        eval ("\$wanted_bit .= \"" . $templates->get("wanted_bit_global") . "\";");
    }
    eval ("\$wanted_index = \"" . $templates->get("wanted_global") . "\";");
      if ($show_cat == 0) {
    eval ("\$wanted_global = \"" . $templates->get("wanted_global") . "\";");
      }
}

function wanted_build_forumbits_forum(&$forum)
{
    global $wanted_index, $mybb;
    $forum_cat = $mybb->settings['wanted_cat'];
    $show_cat = $mybb->settings['wanted_above_cat'];
    if ($show_cat == 1) {
        if ($forum['fid'] == $forum_cat) {
            $forum['wanted'] = $wanted_index;
        }
    }
    return $forum;
}

function wanted_profile()
{
    global $db, $mybb, $templates, $lang, $memprofile, $wanted_profile ;
    $lang->load('wanted');
    // variabeln leeren
    $wanted_foren = "";
    $uid = 0;
    // variabel füllen
    $wanted_foren = $mybb->settings['wanted_foren'];
    $uid = $memprofile['uid'];
    if ($mybb->settings['wanted_profile'] == 1) {
        $wantedquery = $db->query("SELECT *
    FROM " . TABLE_PREFIX . "threads
    where fid in ('{$wanted_foren}')
    and uid = '{$uid}'
    ORDER BY subject ASC
    ");

        while ($row = $db->fetch_array($wantedquery)) {
            $wantedlink = "";
            $relation = "";
            $age = "";
            $job = "";
            $info = "";
            $avatar = "";
            $wantedlink = "<a href='showthread.php?tid={$row['tid']}'>{$row['subject']}</a>";
            $relation = $row['relation'];
            $age = $row['age'];
            $job = $row['job'];
            $info = $row['info'];
            $avatar = $row['avatarperson'];
            eval ("\$wanted_profile_bit .= \"" . $templates->get("wanted_profile_bit") . "\";");
        }
        eval ("\$wanted_profile = \"" . $templates->get("wanted_profile") . "\";");
    }

}
