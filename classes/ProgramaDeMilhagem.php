<?php


require_once("passageiroVip.php");
require_once("categoria.php");
require_once "log.php";

class ProgramaDeMilhagem
{
    /**
     * @var Categoria[]
     */
    private array $categorias;
    private string $nome_do_programa;

    /**
     * @param Categoria[] $categorias
     * @param string $nome_do_programa
     * @throws InvalidArgumentException
     */
    public function __construct(array $categorias, string $nome_do_programa)
    {
        $this->categorias = ProgramaDeMilhagem::validaCategorias($categorias);
        $this->nome_do_programa = ProgramaDeMilhagem::validaNome($nome_do_programa);
    }

    /** Valida que há uma categoria base e que as categorias estão na ordem
     * @param Categoria[] $categorias
     * @return Categoria[]
     * @throws InvalidArgumentException
     */
    private static function validaCategorias(array $categorias): array
    {
        $length = count($categorias);
        if ($length == 0) {
            log::getInstance()->logThrow(new InvalidArgumentException("Deve haver pelo menos uma categoria"));
        }
        if ($categorias[0]->getPontuacao() != 0) {
            log::getInstance()->logThrow(new InvalidArgumentException("A primeira categoria deve ter 0 pontos"));
        }
        if ($length == 1) {
            log::getInstance()->logCall($categorias);
        }

        # Checa se array está em ordem
        for ($i = 1; $i < $length; $i++) {
            if ($categorias[$i]->getPontuacao() < $categorias[$i - 1]->getPontuacao()) {
                // Se qualquer elemento tem a pontucao menor que o elemento anterior, a array nao está em ordem
                log::getInstance()->logThrow(new InvalidArgumentException("As categorias não estão em ordem"));
            }
        }

        return log::getInstance()->logCall($categorias);
    }

    /** Valida o nome
     * @param string $nome
     * @return string
     * @throws InvalidArgumentException
     */
    private static function validaNome(string $nome): string
    {
        if (empty($nome)) {
            log::getInstance()->logThrow(new InvalidArgumentException("O programa deve ter um nome"));
        }
        return log::getInstance()->logCall($nome);
    }

    public function getCategorias(): array
    {
        return log::getInstance()->logCall($this->categorias);
    }

    public function getNomeDoPrograma(): string
    {
        return log::getInstance()->logCall($this->nome_do_programa);
    }

    public function update(PassageiroVip $passageiro): Categoria|null
    {
        $categoriaDoPassageiro = $passageiro->getCategoriaDoPrograma();
        $categoria = $this->categorias[0];
        $pontuacao = $passageiro->getPontosValidos();
        foreach ($this->categorias as $categoriaAlvo) {
            if ($categoriaAlvo->getPontuacao() <= $pontuacao) {
                $categoria = $categoriaAlvo;
            }
        }
        if (count($categoriaDoPassageiro) > 1 and $categoriaDoPassageiro[count($categoriaDoPassageiro) - 1] === $categoria) {
            return log::getInstance()->logCall($categoria);
        }
        $passageiro->alterarCategoria($categoria);
        return log::getInstance()->logCall($categoria);
    }
}

?>