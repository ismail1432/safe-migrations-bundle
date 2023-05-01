<?php

namespace App\resources;

class Version20230501CustomStatement
{
    public function getDescription(): string
    {
        return 'CUSTOM STATEMENT';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CUSTOM STATEMENT has to be check');
    }

    public function down(): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dummy DROP dummy_column');
    }
}
