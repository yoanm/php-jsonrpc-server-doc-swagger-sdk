<?php
namespace Yoanm\JsonRpcHttpServerSwaggerDoc\App\Normalizer\Component;

use Yoanm\JsonRpcHttpServerSwaggerDoc\App\Resolver\DefinitionRefResolver;
use Yoanm\JsonRpcServerDoc\Domain\Model\MethodDoc;

/**
 * Class RequestDocNormalizer
 */
class RequestDocNormalizer
{
    /** @var DefinitionRefResolver */
    private $definitionRefResolver;
    /** @var ShapeNormalizer */
    private $shapeNormalizer;

    /**
     * @param DefinitionRefResolver $definitionRefResolver
     * @param ShapeNormalizer       $shapeNormalizer
     */
    public function __construct(
        DefinitionRefResolver $definitionRefResolver,
        ShapeNormalizer $shapeNormalizer
    ) {
        $this->definitionRefResolver = $definitionRefResolver;
        $this->shapeNormalizer = $shapeNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(MethodDoc $method)
    {
        $requestSchema = ['allOf' => [$this->shapeNormalizer->getRequestShapeDefinition()]];
        // Append custom if params required
        if (null !== $method->getParamsDoc()) {
            $methodParamsDefinitionRef = $this->definitionRefResolver->getDefinitionRef(
                $this->definitionRefResolver->getMethodDefinitionId(
                    $method,
                    DefinitionRefResolver::METHOD_PARAMS_DEFINITION_TYPE
                )
            );

            $requestSchema['allOf'][] = [
                'type' => 'object',
                'required' => ['params'],
                'properties' => [
                    'params' => ['$ref' => $methodParamsDefinitionRef],
                ],
            ];
        }
        $requestSchema['allOf'][] = [
            'type' => 'object',
            'properties' => [
                'method' => ['example' => $method->getMethodName()],
            ],
        ];

        return $requestSchema;
    }
}
