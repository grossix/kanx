<?php

class Cfg {

	public $commands = [];

	public function __construct() {
		$this->commands = [
			// ESL - www.eslgaming.com
			// CS:GO 3on3/5on5 Ladder Config
			// 14.01.2016

			'ammo_grenade_limit_default 1'.
			'ammo_grenade_limit_flashbang 2'.
			'ammo_grenade_limit_total 4'.

			'bot_quota ' . BOT_QUOTA,		// Determines the total number of bots in the game

			'cash_player_bomb_defused 300',
			'cash_player_bomb_planted 300',
			'cash_player_damage_hostage -30',
			'cash_player_interact_with_hostage 150',
			'cash_player_killed_enemy_default 300',
			'cash_player_killed_enemy_factor 1',
			'cash_player_killed_hostage -1000',
			'cash_player_killed_teammate -300',
			'cash_player_rescued_hostage 1000',
			'cash_team_elimination_bomb_map 3250',
			'cash_team_hostage_alive 150',
			'cash_team_hostage_interaction 150',
			'cash_team_loser_bonus 1400',
			'cash_team_loser_bonus_consecutive_rounds 500',
			'cash_team_planted_bomb_but_defused 800',
			'cash_team_rescued_hostage 750',
			'cash_team_terrorist_win_bomb 3500',
			'cash_team_win_by_defusing_bomb 3500',
			'cash_team_win_by_hostage_rescue 3500',
			'cash_player_get_killed 0',
			'cash_player_respawn_amount 0',
			'cash_team_elimination_hostage_map_ct 2000',
			'cash_team_elimination_hostage_map_t 1000',
			'cash_team_win_by_time_running_out_bomb 3250',
			'cash_team_win_by_time_running_out_hostage 3250',


			'ff_damage_reduction_grenade 0.85',           	// How much to reduce damage done to teammates by a thrown grenade.  Range is from 0 - 1 (with 1 being damage equal to what is done to an enemy)
			'ff_damage_reduction_bullets 0.33',           	// How much to reduce damage done to teammates when shot.  Range is from 0 - 1 (with 1 being damage equal to what is done to an enemy)
			'ff_damage_reduction_other 0.4',              	// How much to reduce damage done to teammates by things other than bullets and grenades.  Range is from 0 - 1 (with 1 being damage equal to what is done to an enemy)
			'ff_damage_reduction_grenade_self 1',         	// How much to damage a player does to himself with his own grenade.  Range is from 0 - 1 (with 1 being damage equal to what is done to an enemy)

			'mp_afterroundmoney 0',				// amount of money awared to every player after each round
			'mp_autokick 0',					// Kick idle/team-killing players
			'mp_autoteambalance 0',
			'mp_buytime 15',                           	// How many seconds after round start players can buy items for.
			'mp_c4timer 40',                           	// How long from when the C4 is armed until it blows
			'mp_death_drop_defuser 1',				// Drop defuser on player death
			'mp_death_drop_grenade 2',				// Which grenade to drop on player death: 0=none, 1=best, 2=current or best
			'mp_death_drop_gun 1',				// Which gun to drop on player death: 0=none, 1=best, 2=current or best
			'mp_defuser_allocation 0',				// How to allocate defusers to CTs at start or round: 0=none, 1=random, 2=everyone
			'mp_do_warmup_period 1',				// Whether or not to do a warmup period at the start of a match.
			'mp_forcecamera 1',                        	// Restricts spectator modes for dead players
			'mp_force_pick_time 160',				// The amount of time a player has on the team screen to make a selection before being auto-teamed 
			'mp_free_armor 0',					// Determines whether armor and helmet are given automatically.
			'mp_freezetime 12',                        	// How many seconds to keep players frozen when the round starts
			'mp_friendlyfire 1',                       	// Allows team members to injure other members of their team
			'mp_halftime 1',					// Determines whether or not the match has a team-swapping halftime event.
			'mp_halftime_duration 15',				// Number of seconds that halftime lasts
			'mp_join_grace_time 30',				// Number of seconds after round start to allow a player to join a game
			'mp_limitteams 0',                         	// Max # of players 1 team can have over another (0 disables check)
			'mp_logdetail 3',                          	// Logs attacks.  Values are: 0=off, 1=enemy, 2=teammate, 3=both)
			'mp_match_can_clinch 1',				// Can a team clinch and end the match by being so far ahead that the other team has no way to catching up
			'mp_match_end_restart 1',				// At the end of the match, perform a restart instead of loading a new map
			'mp_maxmoney 16000',				// maximum amount of money allowed in a player's account
			'mp_maxrounds ' . MAX_ROUNDS, //30                         	// max number of rounds to play before server changes maps
			'mp_molotovusedelay 0',                    	// Number of seconds to delay before the molotov can be used after acquiring it
			'mp_playercashawards 1',				// Players can earn money by performing in-game actions
			'mp_playerid 0',					// Controls what information player see in the status bar: 0 all names; 1 team names; 2 no names 
			'mp_playerid_delay 0.5',				// Number of seconds to delay showing information in the status bar
			'mp_playerid_hold 0.25',				// Number of seconds to keep showing old information in the status bar
			'mp_round_restart_delay 5',			// Number of seconds to delay before restarting a round after a win
			'mp_roundtime 1.92',                       	// How many minutes each round takes.
			'mp_roundtime_defuse 1.92',                	// How many minutes each round takes on defusal maps.
			'mp_solid_teammates 1', 				// Determines whether teammates are solid or not.
			'mp_startmoney 800',                       	// amount of money each player gets when they reset
			'mp_teamcashawards 1',				// Teams can earn money by performing in-game actions
			'mp_timelimit 0',                           	// game time per map in minutes
			'mp_tkpunish 0',					// Will a TK'er be punished in the next round?  {0=no,  1=yes}
			'mp_warmuptime 60', //1					// If true, there will be a warmup period/round at the start of each match to allow
			'mp_weapons_allow_map_placed 1',             	// If this convar is set, when a match starts, the game will not delete weapons placed in the map.
			'mp_weapons_allow_zeus 1',				// Determines whether the Zeus is purchasable or not.
			'mp_win_panel_display_time 15',	                // The amount of time to show the win panel between matches / halfs

			'spec_freeze_time 2.0',                            // Time spend frozen in observer freeze cam.
			'spec_freeze_panel_extended_time 0',               // Time spent with the freeze panel still up after observer freeze cam is done.
			'spec_freeze_time_lock 2',
			'spec_freeze_deathanim_time 0',

			'sv_accelerate 5.5',			        // ( def. "10" ) client notify replicated 
			'sv_stopspeed 80',					//
			'sv_allow_votes 0',				// Allow voting?
			'sv_allow_wait_command 0',			        // Allow or disallow the wait command on clients connected to this server.
			'sv_alltalk 0',					// Players can hear all other players' voice communication, no team restrictions
			'sv_alternateticks 0',				// If set, server only simulates entities on even numbered ticks.
			'sv_cheats 0',                                     // Allow cheats on server
			'sv_clockcorrection_msecs 15',                     // The server tries to keep each player's m_nTickBase withing this many msecs of the server absolute tickcount
			'sv_consistency 0',				// Whether the server enforces file consistency for critical files
			'sv_contact 0',					// Contact email for server sysop
			'sv_damage_print_enable 0',                        // Turn this off to disable the player's damage feed in the console after getting killed.
			'sv_dc_friends_reqd 0',			        // Set this to 0 to allow direct connects to a game in progress even if no presents
			'sv_deadtalk 0',					// Dead players can speak (voice, text) to the living
			'sv_forcepreload 0',				// Force server side preloading.
			'sv_friction 5.2',					// World friction.
			'sv_full_alltalk 0',				// Any player (including Spectator team) can speak to any other player
			'sv_gameinstructor_disable 1',		        // Force all clients to disable their game instructors.
			'sv_ignoregrenaderadio 0',                         // Turn off Fire in the hole messages
			'sv_kick_players_with_cooldown 0',                 // (0: do not kick; 1: kick Untrusted players; 2: kick players with any cooldown)
			'sv_kick_ban_duration 0',                          // How long should a kick ban from the server should last (in minutes)
			'sv_lan 0',                                        // Server is a lan server ( no heartbeat, no authentication, no non-class C addresses )
			'sv_log_onefile 0',				// Log server information to only one file.
			'sv_logbans 1',					// Log server bans in the server logs.
			'sv_logecho 1',					// Echo log information to the console.
			'sv_logfile 1',					// Log server information in the log file.
			'sv_logflush 0',					// Flush the log file to disk on each write (slow).
			'sv_logsdir logfiles',                             // Folder in the game directory where server logs will be stored.
			'sv_maxrate 0',					// min. 0.000000 max. 30000.000000 replicated  Max bandwidth rate allowed on server, 0 == unlimited
			'sv_mincmdrate 30',				// This sets the minimum value for cl_cmdrate. 0 == unlimited.
			'sv_minrate 20000',				// Min bandwidth rate allowed on server, 0 == unlimited
			'sv_competitive_minspec 1',                        // Enable to force certain client convars to minimum/maximum values to help prevent competitive advantages.
			'sv_competitive_official_5v5 1',			// Enable to force the server to show 5v5 scoreboards and allows spectators to see characters through walls.
			'sv_pausable 1',                                   // Is the server pausable.
			'sv_pure 1',
			'sv_pure_kick_clients 1',                          // If set to 1, the server will kick clients with mismatching files. Otherwise, it will issue a warning to the client.
			'sv_pure_trace 0',					// If set to 1, the server will print a message whenever a client is verifying a CR
			'sv_spawn_afk_bomb_drop_time 30',                 	// Players that spawn and don't move for longer than sv_spawn_afk_bomb_drop_time (default 15 seconds) will automatically drop the bomb.
			'sv_steamgroup_exclusive 0',                     	// If set, only members of Steam group will be able to join the server when it's empty, public people will be able to join the server only if it has players.
			'sv_voiceenable 1',
			'sv_alltalk 0',
			'sv_auto_full_alltalk_during_warmup_half_end 0',

			//Overtime
			'mp_overtime_enable 1',
			'mp_overtime_maxrounds ' . MAX_OVERTIME_ROUNDS,
			'mp_overtime_startmoney 10000',

			'say "> ESL CS:GO 3on3/5on5 Ladder Config loaded - 14.01.2016<"'
		];
	}

}
