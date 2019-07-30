<?php

    namespace App\Controller\Traits;

    /**
     * Créer ici toutes les fonctions qui ne sont pas expressément liées à un controller
     */
    trait Util {

        /**
         * Extrait l'id de la video Youtube
         *
         * @param string $lienYT
         */
        private function extractYoutubeVariable(string $lienYT)
        {
            $v = strpos($lienYT, 'v=');
            $et = strpos($lienYT, '&', $v);

            if($et > 0 && $v > 0) {
                $offset = $et - $v-2;
            } else {
                $offset = 11;
            }

            $resultat = substr($lienYT, $v+2, $offset);
            return $resultat;
        }
    }