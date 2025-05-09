<?php

namespace WechatWorkMediaBundle\Tests\Procedure;

use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Exception\ApiException;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkMediaBundle\Enum\MediaType;
use WechatWorkMediaBundle\Procedure\TransformFileToWechatWorkMaterial;
use WechatWorkMediaBundle\Service\MediaService;

class TransformFileToWechatWorkMaterialTest extends TestCase
{
    private TransformFileToWechatWorkMaterial $procedure;
    private CorpRepository $corpRepository;
    private AgentRepository $agentRepository;
    private FilesystemOperator $mountManager;
    private MediaService $mediaService;
    
    protected function setUp(): void
    {
        $this->corpRepository = $this->createMock(CorpRepository::class);
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->mountManager = $this->getMockBuilder(FilesystemOperator::class)
            ->disableOriginalConstructor()
            ->addMethods(['getLocalPath'])
            ->getMockForAbstractClass();
        $this->mediaService = $this->createMock(MediaService::class);
        
        $this->procedure = new TransformFileToWechatWorkMaterial(
            $this->corpRepository,
            $this->agentRepository,
            $this->mountManager,
            $this->mediaService
        );
    }
    
    public function testExecute_withValidInput(): void
    {
        // 设置测试数据
        $corpId = 'test_corp_id';
        $agentId = 'test_agent_id';
        $fileUrl = 'https://example.com/test.jpg';
        $mediaType = MediaType::IMAGE->value;
        $localPath = '/local/path/to/file.jpg';
        $mediaId = 'generated_media_id_123';
        
        // 设置过程参数
        $this->procedure->corpId = $corpId;
        $this->procedure->agentId = $agentId;
        $this->procedure->fileUrl = $fileUrl;
        $this->procedure->mediaType = $mediaType;
        
        // 创建模拟对象
        $corp = new Corp();
        $agent = new Agent();
        
        // 模拟企业查询
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => $corpId])
            ->willReturn($corp);
            
        // 模拟应用查询
        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corp' => $corp, 'agentId' => $agentId])
            ->willReturn($agent);
            
        // 模拟获取本地路径
        $this->mountManager->expects($this->once())
            ->method('getLocalPath')
            ->with($fileUrl)
            ->willReturn($localPath);
            
        // 模拟媒体上传
        $this->mediaService->expects($this->once())
            ->method('uploadAndGetMediaId')
            ->with(
                $agent,
                $localPath,
                $this->callback(function($mediaTypeEnum) use ($mediaType) {
                    return $mediaTypeEnum->value === $mediaType;
                })
            )
            ->willReturn($mediaId);
            
        // 执行过程
        $result = $this->procedure->execute();
        
        // 验证结果
        $this->assertIsArray($result);
        $this->assertArrayHasKey('media_id', $result);
        $this->assertEquals($mediaId, $result['media_id']);
    }
    
    public function testExecute_withInvalidCorp(): void
    {
        // 设置测试数据
        $corpId = 'invalid_corp_id';
        $agentId = 'test_agent_id';
        $fileUrl = 'https://example.com/test.jpg';
        $mediaType = MediaType::IMAGE->value;
        
        // 设置过程参数
        $this->procedure->corpId = $corpId;
        $this->procedure->agentId = $agentId;
        $this->procedure->fileUrl = $fileUrl;
        $this->procedure->mediaType = $mediaType;
        
        // 模拟企业查询 - 返回空
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => $corpId])
            ->willReturn(null);
            
        // 预期抛出异常
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('找不到企业信息');
        
        // 执行过程
        $this->procedure->execute();
    }
    
    public function testExecute_withInvalidAgent(): void
    {
        // 设置测试数据
        $corpId = 'test_corp_id';
        $agentId = 'invalid_agent_id';
        $fileUrl = 'https://example.com/test.jpg';
        $mediaType = MediaType::IMAGE->value;
        
        // 设置过程参数
        $this->procedure->corpId = $corpId;
        $this->procedure->agentId = $agentId;
        $this->procedure->fileUrl = $fileUrl;
        $this->procedure->mediaType = $mediaType;
        
        // 创建模拟对象
        $corp = new Corp();
        
        // 模拟企业查询
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => $corpId])
            ->willReturn($corp);
            
        // 模拟应用查询 - 返回空
        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corp' => $corp, 'agentId' => $agentId])
            ->willReturn(null);
            
        // 预期抛出异常
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('找不到应用信息');
        
        // 执行过程
        $this->procedure->execute();
    }
    
    /**
     * 测试调用 tryFrom 方法时的错误情况
     * 
     * 注意：MediaType::tryFrom 在传入无效值时可能返回 null，
     * 这会导致在 uploadAndGetMediaId 调用时出现 TypeError
     */
    public function testExecute_withInvalidMediaType(): void
    {
        // 设置测试数据
        $corpId = 'test_corp_id';
        $agentId = 'test_agent_id';
        $fileUrl = 'https://example.com/test.jpg';
        $mediaType = 'invalid_media_type'; // 无效的媒体类型
        
        // 设置过程参数
        $this->procedure->corpId = $corpId;
        $this->procedure->agentId = $agentId;
        $this->procedure->fileUrl = $fileUrl;
        $this->procedure->mediaType = $mediaType;
        
        // 创建模拟对象
        $corp = new Corp();
        $agent = new Agent();
        
        // 模拟企业查询
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => $corpId])
            ->willReturn($corp);
            
        // 模拟应用查询
        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corp' => $corp, 'agentId' => $agentId])
            ->willReturn($agent);
            
        // 模拟获取本地路径
        $this->mountManager->expects($this->once())
            ->method('getLocalPath')
            ->with($fileUrl)
            ->willReturn('/local/path/to/file.jpg');
        
        // 预期抛出 TypeError 异常，而不是 ValueError
        $this->expectException(\TypeError::class);
        
        // 执行过程
        $this->procedure->execute();
    }
} 