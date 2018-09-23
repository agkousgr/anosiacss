<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CsvImportCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;


    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setName('csv:import')
            ->setDescription('Csv import');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ip = new SymfonyStyle($input, $output);

        $ip->title('Starting importing...');

        $csv = Reader::createFromPath('%kernel.root_dir%/../public/csv/articles.csv');
        $csv->setHeaderOffset(0);

        $stmt = (new Statement())
            ->limit(500)
        ;

        $records = $stmt->process($csv);
        foreach ($records as $record) {
            dump($record);
        }

        $ip->success('Import completed succesfully');
    }
}