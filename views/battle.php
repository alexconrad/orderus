<html>
<head>
	<title>Battle Setup</title>
	<meta charset="utf-8"/>
	<link rel="stylesheet" type="text/css" href="./css/style.css" />

	<script src="./js/jquery-1.12.3.min.js" type="text/javascript"></script>
	<script type="text/javascript">
<?php

/** @noinspection PhpUndefinedVariableInspection */
$aOriginalMembers = $originalMembers;
$aTeams = [];
foreach ($aOriginalMembers as $oOriginalMember) {
	$aTeams[$oOriginalMember->team][$oOriginalMember->name] = $oOriginalMember;
}

?>

var currentTurn = 0;
var aRounds = <?php echo json_encode($aRounds); ?>;
var aTeams = JSON.parse('<?php echo json_encode($aTeams); ?>');

var TotalRounds = aRounds.length;

var TotalTurns = 0;
$.each(aRounds, function (roundNr, oRound) {
	TotalTurns += oRound.turns.length;
});


/*
$("#previousTurn").click(function () {
	var prev = currentTurn - 1;
	if (prev < 0) {
		prev = 0;
	}
	showTurn(prev);
	currentTurn = prev;
});
*/



function searchMember(sName) {
	var found = "";
	$.each(aTeams, function (team, members) {
		$.each(members, function (mName, mData) {
			if (sName == mName) {
				found = mData;
			}
		});
	});
	return found;
}

function showEndGame() {
	$("#RoundDataContainer").css("display","none");
	$("#EndGame").css("display","block").find("#NrTurns").text(TotalTurns);
}

function showTurn(idx) {

	$("#EndGame").css("display","none");

	var cnt = 0;
	var chosenRound = 0;
	var chosenTurn = 0;
	$.each(aRounds, function (roundNr, oRound) {
		$.each(oRound.turns, function (turnNr, oTurn) {
			if (cnt == idx) {
				chosenRound = roundNr;
				chosenTurn = turnNr;
			}
			cnt++;
		});
	});

	$("#CurrentTurn").text((idx+1) + "/" + TotalTurns);

	var Rdc = $("#RoundDataContainer");

	if (idx != 0) {
		$.when(Rdc.children().fadeOut(150)).done(function () {
			showRound(chosenRound, chosenTurn);
			Rdc.css("display", "block");
		});
	}else {
		showRound(chosenRound, chosenTurn);
		Rdc.css("display", "block");
	}


}



function showRound(roundIdx, turnIdx) {

	console.log(roundIdx+":"+turnIdx);
	$("#CurrentRoundNumber").text(roundIdx+1);
	$("#TotalNumberOfRounds").text(TotalRounds);

	$("#CurrentRoundTurnNumber").text(turnIdx+1);
	$("#TotalRoundTurns").text(aRounds[roundIdx].turns.length);

	$("#AttackingMemberName").text(aRounds[roundIdx].turns[turnIdx].attacker);
	$("#AttackingMemberHealth").html("Health: " + aRounds[roundIdx].turns[turnIdx].attackerStart.stats.Health + "/" + searchMember(aRounds[roundIdx].turns[turnIdx].attacker).stats.Health
	+ "<br/>Str:" + aRounds[roundIdx].turns[turnIdx].attackerStart.stats.Strength
	);


	$.each(aTeams, function (index, oTeam) {
		$.each(oTeam, function (index2, oMember) {
			$("#j"+oMember.name).css("font-weight", "normal");
		});
	});
	$("#j"+aRounds[roundIdx].turns[turnIdx].attacker).css("font-weight", "bold");


	$("#DefenderMemberName").text(aRounds[roundIdx].turns[turnIdx].defender);
	$("#DefenderMemberHealth").html("Health: " + aRounds[roundIdx].turns[turnIdx].defenderStart.stats.Health + "/" + searchMember(aRounds[roundIdx].turns[turnIdx].defender).stats.Health
		+ "<br/>Def:" + aRounds[roundIdx].turns[turnIdx].defenderStart.stats.Defence
	);
	$("#j"+aRounds[roundIdx].turns[turnIdx].defender).css("font-weight", "bold");

	$("#TurnText").empty();
	$.each(aRounds[roundIdx].turns[turnIdx].aLog, function (index, line) {
		$("#TurnText").append('<div>' + line + '</div>');
	});

	$("#AttackingMemberNameEnd").text(aRounds[roundIdx].turns[turnIdx].attacker);
	$("#AttackingMemberHealthEnd").text("Health: " + aRounds[roundIdx].turns[turnIdx].attackerEnd.stats.Health + "/" + searchMember(aRounds[roundIdx].turns[turnIdx].attacker).stats.Health);

	$("#DefenderMemberNameEnd").text(aRounds[roundIdx].turns[turnIdx].defender);
	$("#DefenderMemberHealthEnd").text("Health: " + aRounds[roundIdx].turns[turnIdx].defenderEnd.stats.Health + "/" + searchMember(aRounds[roundIdx].turns[turnIdx].defender).stats.Health);

	if (aRounds[roundIdx].turns[turnIdx].defenderEnd.stats.Health <= 0) {
		$("#DefenderDead").css("display", "inline");
	}else {
		$("#DefenderDead").css("display", "none");
	}

	var newULOrder = $("#RoundOrderList").clone();
	//newULOrder.attr("id", "ul"+roundIdx+"_"+turnIdx);

	var newLiTitle = $("#RoundOrderTitle").clone();
	//newLiTitle.attr("id", "lit"+roundIdx+"_"+turnIdx);

	newULOrder.empty();
	newULOrder.append(newLiTitle);

	$.each(aRounds[roundIdx].order, function (index, mName) {
		var newLi;
		if (mName == aRounds[roundIdx].turns[turnIdx].attacker) {
			newLi = $("#RoundOrderAttack").clone(); //Attack
		}else {

			//check if dead
			if (aRounds[roundIdx].turns[turnIdx].membersStart[mName].stats.Health <= 0) {
				newLi = $("#RoundOrderMemberDead").clone(); //Normal
			}else {
				newLi = $("#RoundOrderMember").clone(); //Normal
			}
		}
		newLi.text(mName + " ("+searchMember(mName).stats.Speed+")");
		newULOrder.append(newLi);
	});

	$("#RoundOrder").empty().append(newULOrder);


	var newTeamUL = $("#TurnTeamList").clone();
	newTeamUL.empty();

	var newTeamTitle = $("#TurnTeamListTitle").clone();

	var newAttacker = $("#TurnTeamListAttacker").clone();
	var newDefender = $("#TurnTeamListDefender").clone();

	$("#TurnTeamContainer").empty();

	$.each(aTeams, function (teamName, aMembers) {

		var newTeamUL2 = newTeamUL.clone();
		var newTeamTitle2 = newTeamTitle.clone();

		newTeamTitle2.text(teamName);

		newTeamUL2.append(newTeamTitle2);

		$.each(aMembers, function (mName, oMember) {

			var newLi;
			if (mName == aRounds[roundIdx].turns[turnIdx].attacker) {
				newLi = newAttacker.clone();
				newLi.find("#TurnTeamItemHealth").text( aRounds[roundIdx].turns[turnIdx].membersStart[mName].stats.Health);

			}else if (mName == aRounds[roundIdx].turns[turnIdx].defender) {
				newLi = newDefender.clone();
				newLi.find("#TurnTeamItemHealth").html( aRounds[roundIdx].turns[turnIdx].membersStart[mName].stats.Health + " &raquo; " + aRounds[roundIdx].turns[turnIdx].defenderEnd.stats.Health);

			}else if (parseInt(aRounds[roundIdx].turns[turnIdx].membersStart[mName].stats.Health) <= 0) {
				newLi = $("#TurnTeamListDead").clone();
				newLi.find("#TurnTeamItemHealth").text( aRounds[roundIdx].turns[turnIdx].membersStart[mName].stats.Health);

			}else {
				newLi = $("#TurnTeamListNormal").clone();
				newLi.find("#TurnTeamItemHealth").text( aRounds[roundIdx].turns[turnIdx].membersStart[mName].stats.Health);
			}

			newLi.find("#TurnTeamItemName").text(mName);
			newTeamUL2.append(newLi);

		});

		$("#TurnTeamContainer").append(newTeamUL2);

	});

	console.log("fadeId");
	$("#RoundDataContainer").children().fadeIn(150);

}

$( document ).ready(function() {
		var TotalTurns = 0;
		$.each(aRounds, function (roundNr, oRound) {
			TotalTurns += oRound.turns.length;
		});

	$(document).on('click', '#nextTurn', (function () {
		var nxt = currentTurn + 1;
		if (nxt > (TotalTurns - 1)) {
			currentTurn = TotalTurns - 1;
			showEndGame();
		}else {
			showTurn(nxt);
			currentTurn = nxt;
		}
	}));

	$(document).on('click', '#previousTurn', (function () {
		var nxt = currentTurn - 1;
		if (nxt < 0) {
			currentTurn = 0;
			showEndGame();
		}else {
			showTurn(nxt);
			currentTurn = nxt;
		}
	}));

	$(document).on('click', '#endPT', (function () {
		currentTurn = TotalTurns - 1;
		showTurn(TotalTurns - 1);
	}));
	$(document).on('click', '#endFT', (function () {
		currentTurn = 0;
		showTurn(0);
	}));


		//var aTeams = JSON.parse('<?php echo json_encode($aTeams); ?>');

		$.each(aTeams, function (teamName, aTeamMembers) {

			var newTeam = $("#mTeam").clone();
			newTeam.attr("id", "j"+teamName);
			newTeam.find("#mTeamName").text("Team:" + teamName);

			$.each(aTeamMembers, function (index, oMember) {

				var newMember = $("#mMemberContainer").clone();
				newMember.attr("id", "j"+oMember.name);
				newMember.find("#memberName").html(oMember.name);

				//add stats
				var newStats = $("#mStatList").clone();
				newStats.attr("id", oMember.name+"Stats");
				newStats.empty();

				$.each(oMember.stats, function (statName, statValue) {
					var newStat = $("#mStatListItem").clone();

					newStat.find("#StatName").text(statName);
					newStat.find("#StatAmount").text(statValue);
					newStats.append(newStat);
				});
				newMember.find("#statsContainer").append(newStats);

				//add any skills
				var newSkills = $("#mSkillListItem").clone();
				newSkills.attr("id", oMember.name+"Skills");
				newSkills.empty();

				$.each(oMember.skills, function (skillName, skillChance) {
					var newSkill = $("#mSkillListItem").clone();

					newSkill.find("#SkillName").text(skillName);
					newSkill.find("#SkillChance").text(skillChance);
					newSkills.append(newSkill);
				});
				newMember.find("#skillsContainer").append(newSkills);

				newTeam.append(newMember);
			});
			$("#OrigTeams").append(newTeam);
		});

	showTurn(0);
	$("#RoundDataContainer").css("display","block");

});
	</script>
</head>
<body>
<div id="EndGame" class="RoundDataContainer" style="display: none;">
	<h3>Battle Ended.</h3>
	Still standing after <span id="NrTurns"></span> turn(s):
	<div id="stillStanding"><?php
		/** @noinspection SpellCheckingInspection */
		/** @noinspection PhpUndefinedVariableInspection */
		echo implode("<br>", $stillstanding);
	?></div>
	<br>

	<div class="RoundText">
		<div style="display: inline-block;float: left;"><input type="button" id="endPT" value="LAST TURN"></div>

		<div style="display: inline-block;float: right;"><input type="button" id="endFT" value="FIRST TURN"></div>
	</div>

</div>

<div class="RoundDataContainer" id="RoundDataContainer" style="display: none;">

	<div class="RoundText">
		<div>ROUND <span id="CurrentRoundNumber"></span>/<span id="TotalNumberOfRounds"></span></div>
		<div>TURN <span id="CurrentRoundTurnNumber"></span>/<span id="TotalRoundTurns"></span></div>
	</div>

	<div class="TurnDataContainer">

		<div class="TurnStartContainer">
			<div class="AttackerContainer"><span id="AttackingMemberName" class="AttackingMemberName">MEMBER1</span><span id="AttackingMemberHealth" class="AttackingMemberHealth">Health: 34/900</span></div>
			<div class="TurnStartText">START<br><span style="font-size: 30px;">&#9876;</span></div>
			<div class="DefenderContainer"><span id="DefenderMemberName" class="DefenderMemberName">MEMBER1</span><span id="DefenderMemberHealth"  class="DefenderMemberHealth">Health: 34/900</span></div>
		</div>

		<div class="TurnText" id="TurnText">
		</div>

		<div class="TurnEndContainer">
			<div class="AttackerContainer"><span id="AttackingMemberNameEnd" class="AttackingMemberName">MEMBER1</span><span id="AttackingMemberHealthEnd" class="AttackingMemberHealth">Health: 34/900</span></div>
			<div class="TurnStartText">END<br><span style="font-size: 30px;">&#9876;</span></div>
			<div class="DefenderContainer"><span id="DefenderDead" style="display: none;">&#128128;</span><span id="DefenderMemberNameEnd" class="DefenderMemberName">MEMBER1</span><span id="DefenderMemberHealthEnd"  class="DefenderMemberHealth">Health: 34/900</span></div>
		</div>

	</div>

	<div class="LeftContainer">
		
		<div class="RoundOrder" id="RoundOrder">
			<ul class="RoundOrderList" id="RoundOrderList">
				<li class="RoundOrderTitle" id="RoundOrderTitle">Round order</li>
				<li class="RoundOrderAttack" id="RoundOrderAttack">Member1</li>
				<li class="RoundOrderMember" id="RoundOrderMember">Member1</li>
				<li class="RoundOrderMemberDead" id="RoundOrderMemberDead">Member1</li>
				<li>Member1</li>
			</ul>
		</div>

		<div class="TurnTeams" id="TurnTeams">
			<div class="TurnTeamContainer" id="TurnTeamContainer">
				<ul class="TurnTeamList" id="TurnTeamList">
					<li class="TurnTeamListTitle" id="TurnTeamListTitle">Team</li>

					<li class="TurnTeamListDefender" id="TurnTeamListDefender">
						<span style="display: inline-block;width: 170px;">
							<span id="TurnTeamItemName" style="float: left;">Member1</span>
							<span id="TurnTeamItemHealth" style="float: right;">74 &raquo; 72</span>
						</span>
					</li>
					<li class="TurnTeamListAttacker" id="TurnTeamListAttacker">
						<span style="display: inline-block;width: 170px;">
							<span id="TurnTeamItemName" style="float: left;">Member1</span>
							<span id="TurnTeamItemHealth" style="float: right;">74</span>
						</span>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="RoundText">
		<div style="display: inline-block;float: left;"><input type="button" id="previousTurn" value="PREVIOUS TURN"></div>
		<div id="CurrentTurn" style="display: inline-block;">1/2</div>
		<div style="display: inline-block;float: right;"><input type="button" id="nextTurn" value="NEXT TURN"></div>
	</div>

</div>


<h3>Initial Members:</h3>
<div id="OrigTeams" class="teamContainer"></div>
<a href="index.php">Setup another battle.</a>


<!-- j -->
<div style="display: none;">
	<div id="mTeam" class="mTeam">
		<div id="mTeamName" class="mTeamName"></div>
	</div>

	<div id="mMemberContainer" class="mMember" style="height: 220px;">
		<div id="memberName" class="mMemberName"></div>
		<div id="statsContainer">
		</div>
		<div id="skillsContainer" class="mSkillContainer">
		</div>
	</div>


	<ul id="mStatList" class="mStatList">
		<li id="mStatListItem">
			<span id="StatName" class="mStatName"></span>
			<span id="StatAmount" class="mStatAmount"></span>
		</li>
	</ul>


	<ul id="mSkillListItem" class="mSkillList">
		<li id="mSkillListItem">
			<span id="SkillName" class="mSkillName"></span><span id="SkillChance" class="mSkillAmount"></span>
		</li>
	</ul>

	<ul>
		<li class="TurnTeamListDead" id="TurnTeamListDead">
						<span style="display: inline-block;width: 170px;">
							<span id="TurnTeamItemName" style="float: left;">Member1</span>
							<span id="TurnTeamItemHealth" style="float: right;">72</span>
						</span>
		</li>
		<li class="TurnTeamListNormal" id="TurnTeamListNormal">
						<span style="display: inline-block;width: 170px;">
							<span id="TurnTeamItemName" style="float: left;">Member1</span>
							<span id="TurnTeamItemHealth" style="float: right;">72</span>
						</span>
		</li>
	</ul>
</div>

</body>
</html>
