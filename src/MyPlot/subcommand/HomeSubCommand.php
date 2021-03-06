<?php
namespace MyPlot\subcommand;

use MyPlot\Plot;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class HomeSubCommand extends SubCommand
{
	/**
	 * @param CommandSender $sender
	 * @return bool
	 */
	public function canUse(CommandSender $sender) {
		return ($sender instanceof Player) and $sender->hasPermission("myplot.command.home");
	}

	/**
	 * @param Player $sender
	 * @param string[] $args
	 * @return bool
	 */
	public function execute(CommandSender $sender, array $args) {
		if (empty($args)) {
			$plotNumber = 1;
		} elseif (count($args) === 1 and is_numeric($args[0])) {
			$plotNumber = (int) $args[0];
		} else {
			return false;
		}
		if(isset($args[1])) {
			$plots = $this->getPlugin()->getPlotsOfPlayer($sender->getName(), $args[1]);
		}else{
			$plots = $this->getPlugin()->getPlotsOfPlayer($sender->getName(), $sender->getLevel()->getName());
		}
		if (empty($plots)) {
			$sender->sendMessage(TextFormat::RED . $this->translateString("home.noplots"));
			return true;
		}
		if (!isset($plots[$plotNumber - 1])) {
			$sender->sendMessage(TextFormat::RED . $this->translateString("home.notexist", [$plotNumber]));
			return true;
		}

		usort($plots, function (Plot $plot1, Plot $plot2) {
			if ($plot1->levelName == $plot2->levelName) {
				return 0;
			}
			return ($plot1->levelName < $plot2->levelName) ? -1 : 1;
		});

		$plot = $plots[$plotNumber - 1];
		if ($this->getPlugin()->teleportPlayerToPlot($sender, $plot)) {
			$sender->sendMessage($this->translateString("home.success", [$plot, $plot->levelName]));
		} else {
			$sender->sendMessage(TextFormat::RED . $this->translateString("home.error"));
		}
		return true;
	}
}