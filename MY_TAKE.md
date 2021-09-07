L'idée est que la période mensuelle et l'absence sont toutes les deux des périodes.  
On peut aborder cet aspect sous plusieurs formes:
- 2 objets indépendants
- utiliser l'héritage avec une classe abstraite "Period"
- utiliser la composition avec un trait
- utiliser la composition en injectant un objet period

J'ai choisi un trait au premier abord, dans la mesure où potentiellement, l'objet "période mensuelle" peut devenir plus important, et avoir d'autre comportements qui nécessiteraient d'autres trait.

J'ai ajouté quelques cas de test, lorsqu'une absence est à cheval ou lorsqu'elle est plus grande que le mois.  
On compte également le nombre de jour commun.

Par la suite, on peut très bien imaginer une gestion plus poussée des jours (ouvrés/ouvrables) par exemple.
