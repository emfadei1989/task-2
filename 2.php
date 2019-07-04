<?php

class DomainService
{
    private $bunchSize = 1000;
    private $domains = [];
    private $pdo;
    private $tableName = 'users';
    private $emailField = 'email';

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return array
     */
    public function getDomains(): array
    {
        $offset = 0;
        while (true) {
            $res = $this->pdo->prepare("SELECT {$this->emailField} FROM {$this->tableName}  LIMIT :offset,:bunch");
            $res->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $res->bindValue(':bunch', $this->bunchSize, \PDO::PARAM_INT);
            $res->execute();
            $count = $res->rowCount();
            if (empty($count)) {
                break;
            }
            while ($row = $res->fetch(\PDO::FETCH_ASSOC)) {

                $row_domains = $this->getEmails($row[$this->emailField]);
                foreach ($row_domains as $domain) {
                    $this->add($domain);
                }
            }
            $offset += $this->bunchSize;

        }

        return $this->domains;
    }

    /**
     * @param string $email
     *
     * @return array
     */
    private function getEmails(string $email): array
    {
        preg_match_all('/@(.*?)(?=,|$)/', $email, $domains);
        if (!isset($domains[1])) {
            return array();
        }
        return $domains[1];
    }

    /**
     * @param string $domain
     */
    private function add(string $domain)
    {
        if (!isset($this->domains[$domain])) {
            $this->domains[$domain] = 0;
        }
        ++$this->domains[$domain];
    }
}

$pdo = new \PDO('mysql:host=localhost;dbname=bookparser', 'root', '');
$domainService = new \App\Services\DomainService($pdo);
$domainService->getDomains();
