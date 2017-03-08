## Nomenclature

- reference: a key/value in some data that references another object's identifier, eg ['article_id' => 124]
- referenceKey: the key of a reference, eg 'article_id'
- referenceValue: the value of a reference, eg 124

- association: a key/value in some data that references another object, eg ['article' => (object Article)]
- associationKey: the key of an assocation, eg 'article'
- associationValue: the value of an association, eg (object Article)

- targetClass: className of the referenced object, eg 'AcmeBundle\Entity\Article'
- targetIdentifier: referenced identifier in targetClass, eg 'id'

An association may require more than one reference to be resolved.
