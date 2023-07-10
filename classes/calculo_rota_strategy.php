<?php

require_once 'coordenada.php';
require_once "log.php";

interface CalculoRotaStrategy
{
    /** Calcula a distancia de uma rota
     * @param ICoordenada[] $coordenadas
     * @param ICoordenada $pontoFinal
     * @return float
     */
    function calculaDistanciaTotal(array $coordenadas, ICoordenada $pontoFinal): float;
    /** Calcula a distancia de parciais uma rota
     * @param ICoordenada[] $coordenadas
     * @param ICoordenada $pontoFinal
     * @return float[]
     */
    function calculaDistanciaParciais(array $coordenadas, ICoordenada $pontoFinal): array;
}

class CalculoRotaAproximadaStrategy implements CalculoRotaStrategy
{
    /** Calcula a distancia de parciais uma rota
     * @param ICoordenada[] $coordenadas
     * @param ICoordenada $pontoFinal
     * @return float[]
     */
    function calculaDistanciaParciais(array $coordenadas, ICoordenada $pontoFinal): array {
        if (empty($coordenadas)) {
            return log::getInstance()->logCall([]);
        }
        if (count($coordenadas) == 1) {
            return log::getInstance()->logCall([$this->calculaDistanciaDeDoisPontos($coordenadas[0], $pontoFinal)]);
        }
        /**
         * @var float[] $out
         */
        $out = [];
        $distancia = 0.0;
        $primeiraCoordenada = $coordenadas[0];
        $restoDeCoordenadas = [...array_splice($coordenadas, 1), $pontoFinal];
        $ultimaCoordenada = $primeiraCoordenada;
        foreach ($restoDeCoordenadas as $coordenadaSeguinte) {
            $out[] = $this->calculaDistanciaDeDoisPontos($ultimaCoordenada, $coordenadaSeguinte);
            $ultimaCoordenada = $coordenadaSeguinte;
        }
        return log::getInstance()->logCall($out);
    }
    public function calculaDistanciaTotal(array $coordenadas, ICoordenada $pontoFinal): float
    {
        if (empty($coordenadas)) {
            return log::getInstance()->logCall(0.0);
        }
        if (count($coordenadas) == 1) {
            return log::getInstance()->logCall($this->calculaDistanciaDeDoisPontos($coordenadas[0], $pontoFinal));
        }
        $distancia = 0.0;
        $primeiraCoordenada = $coordenadas[0];
        $restoDeCoordenadas = [...array_splice($coordenadas, 1), $pontoFinal];
        $ultimaCoordenada = $primeiraCoordenada;
        foreach ($restoDeCoordenadas as $coordenadaSeguinte) {
            $distancia += $this->calculaDistanciaDeDoisPontos($ultimaCoordenada, $coordenadaSeguinte);
            $ultimaCoordenada = $coordenadaSeguinte;
        }
        return log::getInstance()->logCall($distancia);
    }

    /** Calcula a distancia aproximada de dois pontos
     * @param ICoordenada $a
     * @param ICoordenada $b
     * @return float
     */
    private function calculaDistanciaDeDoisPontos(ICoordenada $a, ICoordenada $b): float
    {
        return log::getInstance()->logCall(110.57 * sqrt(pow($b->getX() - $a->getX(), 2) + pow($b->getY() - $a->getY(), 2)));
    }
}

?>
