<?php

namespace Slack\Commands;

use Dotenv\Dotenv;
use Slack\Api\Client;
use Slack\Entities\Channel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ChannelsListCommand extends Command
{
  protected function configure ()
  {
    $this
      ->setName("channels:list")

      ->setDescription("This method returns a list of all channels in the team.")

      ->setHelp("Channels included are all channels that the caller is in, channels they are not currently in, and archived channels but does not include private channels. The number of (non-deactivated) members in each channel is also returned.\n\nTo retrieve a list of private channels, use the command groups:list\n\nHaving trouble getting an HTTP 200 response from this command? Try excluding the members list from each channel object using the --exclude_members=0 flag.")

      ->addOption("exclude_archived", null, InputOption::VALUE_REQUIRED, "Exclude archived channels from the listing.", 1)
      ->addOption("exclude_members", null, InputOption::VALUE_REQUIRED, "Exclude the members collection from each channel", 1)
      ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $io = new SymfonyStyle($input, $output);

    $encoders = array(new JsonEncoder);
    $normalizers = array(new ObjectNormalizer);

    $serializer = new Serializer($normalizers, $encoders);

    $env = new Dotenv(__DIR__."/../../../");
    $env->load();

    $client = new Client([
      "base_url" => "https://slack.com/api",
      "token" => getenv("SLACK_API_TOKEN")
    ]);

    $request = $client->ping("channels.list", [
      "exclude_archived" => $input->getOption("exclude_archived"),
      "exclude_members" => $input->getOption("exclude_members"),
    ]);

    $response = (object) [
      "code" => $request->getStatusCode(),
      "body" => $request->getBody(),
    ];

    $channelData = json_decode($response->body)->channels;

    // Deserialize each channel and push them into an array
    $channels = array();
    foreach ($channelData as $channelDataSet):
      $channel = $serializer->deserialize(json_encode($channelDataSet), Channel::class, 'json');

      array_push($channels, $channel);
    endforeach;

    if ($response->code == 200):
      if ($io->isVerbose()):
        $io->text("Response Code: <info>{$response->code}</info>");
        $io->text("URL Method: <comment>{$client->getMethod()}</comment>");
        $io->text("Excluding Archived Channels: <comment>{$input->getOption('exclude_archived')}</comment>");
        $io->text("Excluding Members: <comment>{$input->getOption('exclude_members')}</comment>");
      endif;

      $table = new Table($output);
      $headers = array();
      $rows = array();

      // Grab the attributes of the channel object for use as headers
      $headers = array();
      foreach ($channels[0] as $property => $value):
        array_push($headers, $property);
      endforeach;

      // Insert the values of each channels attributes as row data
      foreach ($channels as $channel):
        $values = array();
        array_push($values, $channel->getId());
        array_push($values, $channel->getName());
        array_push($values, $channel->getIsChannel());
        array_push($values, $channel->getCreated());
        array_push($values, $channel->getCreator());
        array_push($values, $channel->getIsArchived());
        array_push($values, $channel->getIsGeneral());
        array_push($values, $channel->getMembers($input->getOption("exclude_members")));
        array_push($values, $channel->getTopic());
        array_push($values, $channel->getPurpose());
        array_push($values, $channel->getIsMember());
        array_push($values, $channel->getLastRead());
        array_push($values, $channel->getLatest());
        array_push($values, $channel->getUnreadCount());
        array_push($values, $channel->getUnreadCountDisplay());
        array_push($rows, $values);
      endforeach;

      // Render the table output
      $table
        ->setHeaders($headers)
        ->setRows($rows)
      ;
      $table->render();
    else:
      $io->error([$response->code, $response->body]);
    endif;
  }
}
