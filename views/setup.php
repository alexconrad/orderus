<html>
	<head>
		<title>Battle Setup</title>
		<meta charset="utf-8"/>
		<script src="./js/jquery-1.12.3.min.js" type="text/javascript"></script>

		<script type="text/javascript">
			$( document ).ready(function() {

			});

			var aHeroes = [];

			var aStats = ['Health','Strength','Defence','Speed','Luck'];
			var aSkills = ['RapidStrike','MagicShield'];
			var aSkillTypes = {'RapidStrike':'Attack' ,'MagicShield':'Defence'};

			function checkForm() {

				var aTeams = [];
				$('input[name*="[team]"]').each(function () {
					aTeams.push($(this).val());
				});

				aTeams = $.uniqueSort(aTeams);

				if (aTeams.length <= 1) {
					alert('There must be 2 or more teams');
					return false;
				}

				var aNames = [];
				$('input[name*="[name]"]').each(function () {
					aNames.push($(this).val());
				});
				var nLength1 = aNames.length;
				aNames = $.uniqueSort(aNames);
				var nLength2 = aNames.length;

				if (nLength1 != nLength2) {
					alert('Members with the same name detected. Please have unique names for all members.');
					return false;
				}

				return true;
			}


			function addHero(sName) {
				return addMember(sName, 1);
			}

			function addCreep(sName) {
				return addMember(sName, 0);
			}

			function addMember(sName, enableSkills = 0) {

				sName = sName.match(/[A-Za-z0-9]+/).toString();

				if ( $.inArray(sName, aHeroes) != -1) {
					sName = sName + aHeroes.length.toString();
				}

				var oNewHero = $("#memberContainer").clone();
				oNewHero.attr('id', sName);
				oNewHero.css("display", "inline-block");
				oNewHero.find("#inputHeroName").attr("value", sName).attr('name', 'members['+sName+'][name]');
				oNewHero.find("#inputTeamName").attr("value", sName).attr('name', 'members['+sName+'][team]');

				var oNewStatList = $("#statList").clone();
				oNewStatList.attr("id", "list"+sName);
				oNewStatList.css("display", "inline-block");
				oNewStatList.find("#statListItem").remove();
				oNewStatList.empty();

				$.each(aStats, function ( index, value ) {

					var amount = '0';
					if (enableSkills) {
						switch (value) {
							case 'Health':
								amount = '70..100';
								break;
							case 'Strength':
								amount = '70..80';
								break;
							case 'Defence':
								amount = '45..55';
								break;
							case 'Speed':
								amount = '40..50';
								break;
							case 'Luck':
								amount = '10..30';
								break;
							default:
								amount = '10';
						}
					}else {
						switch (value) {
							case 'Health':
								amount = '60..90';
								break;
							case 'Strength':
								amount = '60..90';
								break;
							case 'Defence':
								amount = '40..60';
								break;
							case 'Speed':
								amount = '40..60';
								break;
							case 'Luck':
								amount = '25..40';
								break;
							default:
								amount = '10';
						}

					}

					var oNewStatItem = $("#statListItem").clone();

					oNewStatItem.attr("id", "listStatItem"+sName);
					oNewStatItem.find("#labelStatName").prepend('<span style="width:100px;">['+value+']</span>');
					oNewStatItem.find("#StatName")
						.attr('name', 'members['+sName+'][stat]['+value+']')
						.attr("value", amount);

					oNewStatList.append(oNewStatItem);

					//oNewStatItem = undefined;
				});
				oNewHero.find("#statsContainer").append(oNewStatList);

				var oNewSkillList = $("#skillList").clone();
				oNewSkillList.attr("id", "list"+sName);
				oNewSkillList.css("display", "inline-block");
				oNewSkillList.find("#skillListItem").remove();
				oNewSkillList.empty();

				$.each(aSkills, function ( index, value ) {

					var amount = '0';
					switch (value) {
						case 'RapidStrike': 		amount = '10';break;
						case 'MagicShield':			amount = '20';break;
						default: amount = '10';
					}

					var oNewSkillItem = $("#skillListItem").clone();

					oNewSkillItem.attr("id", "listSkillItem"+sName);
					oNewSkillItem.find("#labelSkillName").prepend('<span style="width:100px;">['+value+']</span>');
					oNewSkillItem.find("#SkillName")
						.attr('name', 'members['+sName+'][skill]['+value+']')
						.attr("value", amount);

					oNewSkillItem.find("#SkillType")
						.attr('name', 'members['+sName+'][skillType]['+value+']')
						.attr("value", aSkillTypes[value]);

					oNewSkillItem.find("#SkillEnabled")
						.attr('name', 'members['+sName+'][skillsEnabled][]')
						.attr("value", value);

					if (enableSkills) {
						oNewSkillItem.find("#SkillEnabled").prop("checked", true);
					}else{
						oNewSkillItem.find("#SkillEnabled").prop("checked", false);
					}

					oNewSkillList.append(oNewSkillItem);

				});


				oNewHero.find("#skillsContainer").append(oNewSkillList);

				oNewStatList = undefined;

				$("#members").append(oNewHero);

				aHeroes.push(sName);
			}


		</script>
		<style type="text/css">
			
			.labelClass {
				display: inline-block;
				width: 240px;
				text-align: right;
			}​

		</style>
	</head>
<body>

<pre>
- The battle can take place between 1 or more teams.
- One Team can have 1 or more Heroes and/or WildBeast.
- To make members be in the same team, change their team name to be the same.
- You can enable Skills on Beasts.
- By default, the Stats and Skills are set to the ones given in the test.
- Rapid Strike is considered a double attack, meaning defender rolls luck and/or skills for every attack.
- Once added, the stats of a Hero/Beast can be changed. Random between A and B should be typed as "A..B". Fixed numbers are fine too.
- You can replay past battles.
</pre>

<form action="index.php?action=battle" method="POST" onsubmit="return checkForm();" id="battleForm">
<input type="button" value="Add Hero" onclick="addHero('OrderUs');">
<input type="button" value="Add Wild Beast" onclick="addCreep('WildBoar');">

<br/><br/>

<div id="members"></div>

Max.Turns:<input type="text" name="maxRounds" value="20"> &nbsp; <input type="submit" name="submit" value="Battle">
</form>

<br>
<?php
arsort($battleLogs);
foreach ($battleLogs as $fname) {
	echo '<a href="index.php?action=replay&fname='.$fname.'">'.$fname.'</a><br>';
}
?>
<br>
<A href="SimpleBattle.php">Simple Battle</a>

<!-- jq to clone -->
<div id="memberContainer" style="display: none;">
	<div id="memberName">
		<label for="memberName" class="labelClass">Hero Name:
			<input id="inputHeroName" type="text" name="" value="" />
		</label>
	</div>
	<div id="teamName">
		<label for="teamName" class="labelClass">Team Name:
			<input id="inputTeamName" type="text" name="" value="" />
		</label>
	</div>
	<div id="statsContainer">
	</div>
	<div id="skillsContainer">
	</div>
</div>

<ul id="statList" style="display: none;">
	<li id="statListItem">
		<div id="statContainer">
				<label for="labelStatName" id="labelStatName" class="labelClass">
				<input type="text" id="StatName" name="" value="" style="width: 90px;text-align: right;" />
				</label>
		</div>
	<li>
</ul>

<ul id="skillList" style="display: none;">
	<li id="skillListItem">
		<div id="skillContainer">
			<label for="labelSkillName" id="labelSkillName" class="labelClass">
				<input type="checkbox" id="SkillEnabled" name="" value="" checked="checked"/>
				<input type="text" id="SkillName" name="" value="" style="width: 90px;text-align: right;" />%
				<input type="hidden" id="SkillType" name="" value="" />
			</label>
		</div>
	<li>
</ul>

</body>
</html>
