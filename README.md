# Gesuchsübersicht
Hier kannst du deine Gesuche Informationen geben und sie auf dem Index per Zufall anzeigen lassen. Die Informationen werden zudem, wenn gewollt, auch im Profil angezeigt. Es ist möglich die Gesuche auf dem Index im Header oder über eine Kategorie anzuzeigen.  
Ihr könnt die Sprachvariabel anpassen, wie ihr möchtet. Es wurde ein extra Feld mit eingefügt, welches hier noch _weitere Informationen_ heißt. Passt die einfach über die Sprachdatei an, wie ihr es braucht. Weitere Felder müssen händisch über die Datenbank hinzugefügt werden und natürlich dann die PHP angepasst werden.

## neue Templates
- wanted_bit_global 	
- wanted_forumdisplay 	
- wanted_global 	
- wanted_global_bit 	
- wanted_infos 	
- wanted_profile 	
- wanted_profile_bit 	
- wanted_showthread
  
## variabeln
**forumbit_depth1_cat** ``{$forum['wanted']}``  
**forumdisplay_thread** ``{$wanted_forumdisplay}``  
**header** ``{$wanted_global}``  
**member_profile** ``{$wanted_profile}``  
**newthread** ``{$wanted_newthread}``  
**showthread** ``{$wanted_showthread}``  

## neue CSS
wanted.css
```
/**Showthread**/
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
```
