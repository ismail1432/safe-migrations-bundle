<?php

namespace App\resources;

class Version20230501CriticalTable
{
    public function getDescription(): string
    {
        return 'CUSTOM STATEMENT';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE my_critical_table DROP dummy_column');
    }

    public function down(): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dummy DROP dummy_column');
    }
}
