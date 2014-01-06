<?php

namespace Ibrows\SonataTranslationBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;

class ORMTranslationAdmin extends TranslationAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter)
    {

    	$em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getEntityManagerForClass('Lexik\Bundle\TranslationBundle\Entity\File');
    	$repo = $em->getRepository('Lexik\Bundle\TranslationBundle\Entity\File');
    	foreach($repo->findAll() as $file){
    		/* @var $file  \Lexik\Bundle\TranslationBundle\Entity\File */
    		$domains[$file->getDomain()] = $file->getDomain();
    	}
    	ksort($domains);
    	$filter
	    	->add('key', 'doctrine_orm_string')
	    	->add('domain', 'doctrine_orm_choice', array(
	    			'field_options'=> array(
	    					'choices' => $domains,
	    					'required' => true,
	    					'multiple' => false,
	    					'expanded' => false,
	    					'empty_value' => 'messages',
	    					'empty_data'  => 'messages'
	    			),
	    			'field_type'=> 'choice',
	    	))
            ->add('content', 'doctrine_orm_callback',
                array
                (
                    'callback' => function(\Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery $queryBuilder, $alias, $field, $options)
                    {
                        /* @var $queryBuilder Sonata\AdminBundle\Datagrid\ORM\ProxyQuery */
                        if(!isset($options['value']) || !$options['value'])
                        {
                            return;
                        }
                        $value = $options['value'];

                        $allreadejoind=false;
                        $joins = $queryBuilder->getDQLPart('join');
                        if(array_key_exists($alias, $joins)){
                            $joins = $joins[$alias];
                            foreach($joins as $join){
                                if(strpos($join->__toString(), "$alias.translations ")){
                                    $allreadejoind = true;
                                }
                            }
                        }
                        if(!$allreadejoind){
                            $queryBuilder->innerJoin(sprintf('%s.translations', $alias), 'translations');
                        }
                        $value = "%$value%";
                        $queryBuilder->andWhere('translations.content LIKE :content');
                        $queryBuilder->setParameter('content', $value);
//                        $queryBuilder->andWhere($queryBuilder->expr()->in('translations.content', $value));
                    },
                    'field_type' => 'text',
                    'label' => 'content'
                )
            )
        ;
    }
}
