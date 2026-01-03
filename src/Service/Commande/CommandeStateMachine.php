<?php
// src/Service/Commande/CommandeStateMachine.php
namespace App\Service\Commande;

use App\Entity\Commande;

class CommandeStateMachine
{
    private const TRANSITIONS = [
        Commande::ETAT_EN_ATTENTE => [
            'VALIDEE' => Commande::ETAT_VALIDEE,
            'ANNULEE' => Commande::ETAT_ANNULEE
        ],
        Commande::ETAT_VALIDEE => [
            'EN_PREPARATION' => Commande::ETAT_EN_PREPARATION,
            'ANNULEE' => Commande::ETAT_ANNULEE
        ],
        Commande::ETAT_EN_PREPARATION => [
            'TERMINEE' => Commande::ETAT_TERMINEE,
            'ANNULEE' => Commande::ETAT_ANNULEE
        ],
        Commande::ETAT_TERMINEE => [
            'EN_LIVRAISON' => Commande::ETAT_EN_LIVRAISON,
            'LIVREE' => Commande::ETAT_LIVREE
        ],
        Commande::ETAT_EN_LIVRAISON => [
            'LIVREE' => Commande::ETAT_LIVREE
        ],
        // États finaux (pas de transitions)
        Commande::ETAT_LIVREE => [],
        Commande::ETAT_ANNULEE => []
    ];

    private const ETAT_LABELS = [
        Commande::ETAT_EN_ATTENTE => 'En attente',
        Commande::ETAT_VALIDEE => 'Validée',
        Commande::ETAT_EN_PREPARATION => 'En préparation',
        Commande::ETAT_TERMINEE => 'Terminée',
        Commande::ETAT_EN_LIVRAISON => 'En livraison',
        Commande::ETAT_LIVREE => 'Livrée',
        Commande::ETAT_ANNULEE => 'Annulée'
    ];

    private const ETAT_COLORS = [
        Commande::ETAT_EN_ATTENTE => 'warning',
        Commande::ETAT_VALIDEE => 'info',
        Commande::ETAT_EN_PREPARATION => 'primary',
        Commande::ETAT_TERMINEE => 'success',
        Commande::ETAT_EN_LIVRAISON => 'secondary',
        Commande::ETAT_LIVREE => 'dark',
        Commande::ETAT_ANNULEE => 'danger'
    ];

    /**
     * Vérifier si une transition est valide
     */
    public function canTransition(string $currentState, string $targetState): bool
    {
        if (!isset(self::TRANSITIONS[$currentState])) {
            return false;
        }

        return in_array($targetState, self::TRANSITIONS[$currentState], true);
    }

    /**
     * Obtenir les transitions possibles depuis un état
     */
    public function getPossibleTransitions(string $currentState): array
    {
        return self::TRANSITIONS[$currentState] ?? [];
    }

    /**
     * Obtenir le label d'un état
     */
    public function getStateLabel(string $state): string
    {
        return self::ETAT_LABELS[$state] ?? $state;
    }

    /**
     * Obtenir la couleur Bootstrap d'un état
     */
    public function getStateColor(string $state): string
    {
        return self::ETAT_COLORS[$state] ?? 'secondary';
    }

    /**
     * Obtenir tous les états avec leurs informations
     */
    public function getAllStates(): array
    {
        $states = [];
        
        foreach (array_keys(self::ETAT_LABELS) as $state) {
            $states[] = [
                'code' => $state,
                'label' => $this->getStateLabel($state),
                'color' => $this->getStateColor($state),
                'transitions' => $this->getPossibleTransitions($state)
            ];
        }
        
        return $states;
    }

    /**
     * Appliquer une transition
     */
    public function applyTransition(Commande $commande, string $targetState): bool
    {
        if (!$this->canTransition($commande->getEtat(), $targetState)) {
            return false;
        }

        $commande->setEtat($targetState);
        
        // Log supplémentaire selon l'état
        switch ($targetState) {
            case Commande::ETAT_EN_LIVRAISON:
                $commande->setUpdatedAt(new \DateTimeImmutable());
                break;
                
            case Commande::ETAT_LIVREE:
                $commande->setUpdatedAt(new \DateTimeImmutable());
                break;
        }

        return true;
    }

    /**
     * Vérifier si une commande peut être annulée
     */
    public function canCancel(Commande $commande): bool
    {
        $cancelableStates = [
            Commande::ETAT_EN_ATTENTE,
            Commande::ETAT_VALIDEE,
            Commande::ETAT_EN_PREPARATION
        ];

        return in_array($commande->getEtat(), $cancelableStates, true);
    }

    /**
     * Vérifier si une commande peut être préparée
     */
    public function canPrepare(Commande $commande): bool
    {
        return $commande->getEtat() === Commande::ETAT_VALIDEE;
    }

    /**
     * Vérifier si une commande peut être terminée
     */
    public function canComplete(Commande $commande): bool
    {
        return $commande->getEtat() === Commande::ETAT_EN_PREPARATION;
    }

    /**
     * Vérifier si une commande peut être livrée
     */
    public function canDeliver(Commande $commande): bool
    {
        return in_array($commande->getEtat(), [
            Commande::ETAT_TERMINEE,
            Commande::ETAT_EN_LIVRAISON
        ], true);
    }

    /**
     * Obtenir les états actifs (non terminés)
     */
    public function getActiveStates(): array
    {
        return [
            Commande::ETAT_EN_ATTENTE,
            Commande::ETAT_VALIDEE,
            Commande::ETAT_EN_PREPARATION,
            Commande::ETAT_TERMINEE,
            Commande::ETAT_EN_LIVRAISON
        ];
    }

    /**
     * Obtenir les états terminés
     */
    public function getCompletedStates(): array
    {
        return [
            Commande::ETAT_LIVREE,
            Commande::ETAT_ANNULEE
        ];
    }
}