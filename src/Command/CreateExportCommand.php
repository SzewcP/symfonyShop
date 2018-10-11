<?php
namespace App\Command;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * @property ContainerInterface container
 */
class CreateExportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:export-product')
            ->setDescription('export product id, name, price')
            ->setHelp('This command allows you to export products to csv file')
            ->addArgument('productId', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'what ids do you wand to export');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Export to csv file',
            '==================',
            '',
            '<info>Finished</info>'
        ]);
        $userId = $input->getArgument('productId');
        $repository = $this->getContainer()->get('doctrine')->getRepository(Product::class);
        if (count($userId) > 0) {
            $product = $repository->findBy(
                ['id' => $userId]
            );
            $fp = fopen('Product.csv', 'w');
            $export = array(
                array('id', 'name', 'price',)
            );
            foreach ($export as $fields) {
                fputcsv($fp, $fields, ';');
            }
            foreach ($product as $product) {
                $id = $product->getId();
                $name = $product->getName();
                $price = $product->getPrice();
                $export = array(
                    array($id, $name, $price,)
                );
                foreach ($export as $fields) {
                    fputcsv($fp, $fields, ';');
                }
            }
            fclose($fp);
        } else {
            $products = $repository->findAll();
            $fp = fopen('Product.csv', 'w');
            $export = array(
                array('id', 'name', 'price',)
            );
            foreach ($export as $fields) {
                fputcsv($fp, $fields, ';');
            }
            foreach ($products as $product) {
                $id = $product->getId();
                $name = $product->getName();
                $price = $product->getPrice();
                $export = array(
                    array($id, $name, $price,)
                );
                foreach ($export as $fields) {
                    fputcsv($fp, $fields, ';');
                }
            }
            fclose($fp);
        }
    }
}