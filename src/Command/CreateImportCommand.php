<?php
namespace App\Command;

use App\Entity\Product;
use League\Csv\Reader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:import-products')
            ->setDescription('import products id, name and price from CSV file')
            ->addArgument('filename', InputArgument::REQUIRED, 'File name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \League\Csv\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename') . '.csv';
        $output->writeln([
            'Import products from csv file',
            '==================',
            '',
            '<info>Finished</info>'
        ]);

        $reader = Reader::createFromPath($filename);
        $reader->setDelimiter(';');
        $reader->setHeaderOffset(0);
        $repository = $this->getContainer()->get('doctrine')->getRepository(Product::class);
        $products = $repository->findAll();

        foreach ($reader as $row) {
            foreach ($products as $product) {
                if ($product->getId() == $row['id']) {
                    $product->setName($row['name']);
                    $product->setPrice($row['price']);
                    $em = $this->getContainer()->get('doctrine')->getManager();
                    $em->flush();
                }
            }

            $product = $repository->findBy(['id' => $row['id']]);
            if (!$product) {
                $product = (new Product())
                    ->setName($row['name'])
                    ->setPrice($row['price']);
                $em = $this->getContainer()->get('doctrine')->getManager();
                $em->persist($product);
                $em->flush();
            }
        }
    }
}


