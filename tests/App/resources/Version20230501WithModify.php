<?php

class Version20230501WithModify
{
    public function getDescription(): string
    {
        return 'Dummy version';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "invoice" MODIFY "title" "New Data Type";');
    }

    public function down(): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
