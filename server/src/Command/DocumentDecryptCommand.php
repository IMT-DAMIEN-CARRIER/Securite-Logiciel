<?php

namespace App\Command;

use App\Repository\DocumentRepository;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DocumentDecryptCommand.
 */
class DocumentDecryptCommand extends Command
{
    /**
     * @var DocumentRepository
     */
    private DocumentRepository $documentRepository;

    /**
     * @var EncryptionService
     */
    private EncryptionService $encryptionService;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    /**
     * DocumentDecryptCommand constructor.
     *
     * @param DocumentRepository     $documentRepository
     * @param EncryptionService      $encryptionService
     * @param EntityManagerInterface $manager
     * @param string|null            $name
     */
    public function __construct(DocumentRepository $documentRepository, EncryptionService $encryptionService, EntityManagerInterface $manager, string $name = null)
    {
        parent::__construct($name);

        $this->documentRepository = $documentRepository;
        $this->encryptionService = $encryptionService;
        $this->manager = $manager;
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('document:decrypt')
            ->setDefinition([
                new InputOption('run', '', InputOption::VALUE_NONE, 'Lance le decryptage'),
            ])
            ->setDescription('Decryptage des documents existant en base de données.')
            ->setHelp(
                <<<'EOF'
The <info>%command.name%</info> command decrypt les certificats déjà présent en base.
EOF
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $run = $input->getOption('run');

        $runBool = false;

        if ('true' === $run || true === $run) {
            $runBool = true;
        }

        $documents = $this->documentRepository->findAll();

        if (!empty($documents)) {
            if ($runBool) {
                foreach ($documents as $document) {

                    $resultDecryption = $this->encryptionService->decrypt($document);

                    if (false !== $resultDecryption) {
                        $document->setContent($resultDecryption);
                        $document->setCryptTag(null);
                    }

                    $this->manager->persist($document);
                }

                $this->manager->flush();
            } else {
                $output->writeln('Lancer la commande avec --run pour exécuter le decryptage.');
            }
        }
    }
}