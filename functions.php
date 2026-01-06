<?php
if (!defined('ABSPATH')) exit;

function dableapproval-theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
}
add_action('after_setup_theme', 'dableapproval-theme_setup');

function dableapproval-theme_scripts() {
    wp_enqueue_style('dableapproval-theme-style', get_stylesheet_uri(), array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'dableapproval-theme_scripts');

function dableapproval-theme_admin_menu() {
    add_menu_page(
        '테마 설정',
        '승인용 테마',
        'manage_options',
        'dableapproval-theme-settings',
        'dableapproval-theme_settings_page',
        'dashicons-admin-generic',
        3
    );
}
add_action('admin_menu', 'dableapproval-theme_admin_menu');

function dableapproval-theme_settings_page() {
    if (isset($_POST['save_settings']) && check_admin_referer('dableapproval-theme_settings_nonce')) {
        if (isset($_POST['custom_head_code'])) {
            update_option('custom_head_code', wp_unslash($_POST['custom_head_code']));
        }
        echo '<div class="notice notice-success"><p>설정이 저장되었습니다.</p></div>';
    }
    
    if (isset($_POST['generate_posts']) && check_admin_referer('dableapproval-theme_generate_nonce')) {
        $count = intval($_POST['post_count']);
        if ($count > 0 && $count <= 100) {
            dableapproval-theme_generate_posts($count);
            echo '<div class="notice notice-success"><p>' . $count . '개의 글이 생성되었습니다.</p></div>';
        }
    }
    
    $head_code = get_option('custom_head_code', '');
    ?>
    <div class="wrap">
        <h1>승인용 테마 설정</h1>
        
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
            <h2>HEAD 코드 관리</h2>
            <p style="color: #666;">애드센스 색인용 코드나 기타 스크립트를 &lt;head&gt; 영역에 삽입합니다.</p>
            <textarea id="custom_head_code" name="custom_head_code" rows="8" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: monospace; font-size: 13px;"><?php echo esc_textarea($head_code); ?></textarea>
            <button onclick="saveHeadCode()" class="button button-primary" style="margin-top: 10px;">HEAD 코드 저장</button>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2>승인용 글 자동 생성</h2>
            <p style="color: #666;">정보성 강한 SEO 최적화 글을 자동 생성합니다. (각 글 1500자 이상, 이모티콘 없음)</p>
            <label style="display: block; margin: 15px 0 10px 0; font-weight: bold;">생성할 글 개수 (1-100):</label>
            <input type="number" id="post_count" name="post_count" min="1" max="100" value="10" style="width: 150px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" />
            <button onclick="generatePosts()" class="button button-primary" style="margin-left: 10px;">글 생성하기</button>
            <div id="generation-status" style="margin-top: 15px; padding: 10px; background: #f0f9ff; border-left: 4px solid #3182f6; display: none;">
                <p style="margin: 0; color: #1e3a8a;">생성 중입니다. 잠시만 기다려주세요...</p>
            </div>
        </div>
        
        <div style="background: #fffbeb; border-left: 4px solid #f59e0b; padding: 20px; margin-top: 20px; border-radius: 4px;">
            <h3 style="margin-top: 0;">💡 사용 가이드</h3>
            <ul style="line-height: 1.8; color: #78350f;">
                <li><strong>HEAD 코드:</strong> 애드센스 색인용 코드나 Google Analytics 등을 삽입할 수 있습니다.</li>
                <li><strong>자동 글 생성:</strong> 버튼 클릭 시 정보성 글이 자동으로 생성됩니다.</li>
                <li><strong>SEO 최적화:</strong> 모든 글은 검색 엔진에 최적화되어 있습니다.</li>
                <li><strong>안전한 콘텐츠:</strong> 위험한 키워드나 이모티콘은 사용하지 않습니다.</li>
            </ul>
        </div>
    </div>
    
    <script>
    function saveHeadCode() {
        var data = new FormData();
        data.append('action', 'save_head_code');
        data.append('nonce', '<?php echo wp_create_nonce("save_head_code_nonce"); ?>');
        data.append('custom_head_code', document.getElementById('custom_head_code').value);
        
        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('HEAD 코드가 저장되었습니다.');
            } else {
                alert('저장 중 오류가 발생했습니다.');
            }
        });
    }
    
    function generatePosts() {
        var count = document.getElementById('post_count').value;
        var status = document.getElementById('generation-status');
        status.style.display = 'block';
        
        var data = new FormData();
        data.append('action', 'generate_approval_posts');
        data.append('nonce', '<?php echo wp_create_nonce("generate_posts_nonce"); ?>');
        data.append('post_count', count);
        
        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(result => {
            status.style.display = 'none';
            if (result.success) {
                alert(count + '개의 글이 생성되었습니다.');
                location.reload();
            } else {
                alert('생성 중 오류가 발생했습니다.');
            }
        });
    }
    </script>
    <?php
}

add_action('wp_ajax_save_head_code', function() {
    check_ajax_referer('save_head_code_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error('권한이 없습니다.');
    }
    if (isset($_POST['custom_head_code'])) {
        update_option('custom_head_code', wp_unslash($_POST['custom_head_code']));
    }
    wp_send_json_success('저장되었습니다.');
});

add_action('wp_ajax_generate_approval_posts', function() {
    check_ajax_referer('generate_posts_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error('권한이 없습니다.');
    }
    $count = intval($_POST['post_count']);
    if ($count > 0 && $count <= 100) {
        dableapproval-theme_generate_posts($count);
        wp_send_json_success($count . '개 생성 완료');
    } else {
        wp_send_json_error('잘못된 개수입니다.');
    }
});

function dableapproval-theme_generate_posts($count) {
    $topics = array(
        array('건강', '건강 관리', '운동', '영양', '질병 예방', '정신 건강'),
        array('요리', '레시피', '식재료', '조리법', '영양소', '식단'),
        array('여행', '관광지', '여행 준비', '문화', '교통', '숙박'),
        array('교육', '학습 방법', '독서', '기술', '언어', '자기계발'),
        array('재무', '저축', '투자', '예산 관리', '금융 지식', '경제'),
        array('취미', '독서', '음악', '미술', '운동', '수집'),
        array('기술', '컴퓨터', '소프트웨어', '인터넷', '프로그래밍', '데이터'),
        array('환경', '재활용', '에너지 절약', '친환경', '지속가능성', '자연'),
        array('생활', '정리정돈', '청소', '시간 관리', '생활 팁', '효율성'),
        array('패션', '의류', '스타일', '트렌드', '액세서리', '코디')
    );
    
    for ($i = 0; $i < $count; $i++) {
        $topic = $topics[array_rand($topics)];
        $main_keyword = $topic[0];
        $sub_keywords = array_slice($topic, 1);
        shuffle($sub_keywords);
        $selected_keywords = array_slice($sub_keywords, 0, 3);
        
        $title = $main_keyword . '에 대한 ' . $selected_keywords[0] . ' 가이드';
        
        $content = '<h2>' . $main_keyword . '의 중요성</h2>';
        $content .= '<p>' . $main_keyword . '는 현대 사회에서 매우 중요한 주제입니다. 많은 사람들이 ' . $main_keyword . '에 대해 관심을 가지고 있으며, 이는 우리의 일상생활에 큰 영향을 미칩니다. ' . $main_keyword . '를 제대로 이해하고 실천하는 것은 더 나은 삶을 위한 첫걸음이 될 수 있습니다. 이 글에서는 ' . $main_keyword . '와 관련된 다양한 측면을 살펴보고, 실질적인 정보를 제공하고자 합니다.</p>';
        
        foreach ($selected_keywords as $keyword) {
            $content .= '<h3>' . $keyword . '에 대한 이해</h3>';
            $content .= '<p>' . $keyword . '는 ' . $main_keyword . '의 중요한 요소 중 하나입니다. ' . $keyword . '를 올바르게 이해하고 적용하면 더 좋은 결과를 얻을 수 있습니다. 많은 전문가들이 ' . $keyword . '의 중요성을 강조하고 있으며, 이는 충분한 근거가 있습니다. ' . $keyword . '에 대해 제대로 알지 못하면 ' . $main_keyword . '를 효과적으로 다루기 어려울 수 있습니다.</p>';
            $content .= '<p>' . $keyword . '를 실천하는 방법은 다양합니다. 첫째로, 기본적인 원리를 이해하는 것이 중요합니다. 둘째로, 꾸준한 연습과 실천이 필요합니다. 셋째로, 전문가의 조언을 참고하는 것이 도움이 됩니다. 넷째로, 자신의 상황에 맞게 적용하는 것이 중요합니다. 다섯째로, 지속적인 개선과 발전을 추구해야 합니다.</p>';
        }
        
        $content .= '<h2>실생활 적용 방법</h2>';
        $content .= '<p>' . $main_keyword . '를 실생활에 적용하는 것은 생각보다 어렵지 않습니다. 작은 변화부터 시작하면 됩니다. 예를 들어, 하루에 조금씩 시간을 내어 ' . $main_keyword . '에 대해 생각해보고, 관련 정보를 찾아보는 것만으로도 큰 도움이 됩니다. 또한, 주변 사람들과 ' . $main_keyword . '에 대해 이야기를 나누면서 새로운 관점을 얻을 수도 있습니다.</p>';
        
        $content .= '<h3>주의사항과 팁</h3>';
        $content .= '<p>' . $main_keyword . '를 다룰 때는 몇 가지 주의사항이 있습니다. 첫째, 과도한 집착은 오히려 역효과를 낼 수 있으므로 균형을 유지하는 것이 중요합니다. 둘째, 자신의 상황과 여건을 고려하여 실천 가능한 범위 내에서 시작해야 합니다. 셋째, 단기간에 큰 변화를 기대하기보다는 장기적인 관점에서 접근하는 것이 좋습니다.</p>';
        
        $content .= '<p>효과적인 ' . $main_keyword . ' 실천을 위한 몇 가지 팁을 소개하겠습니다. 규칙적인 스케줄을 만들어 꾸준히 실천하는 것이 중요합니다. 또한, 목표를 명확히 설정하고 단계별로 달성해 나가는 것이 좋습니다. 주기적으로 자신의 진행 상황을 점검하고 필요한 경우 계획을 수정하는 것도 필요합니다. 무엇보다 긍정적인 마음가짐을 유지하는 것이 성공의 열쇠입니다.</p>';
        
        $content .= '<h2>전문가의 조언</h2>';
        $content .= '<p>많은 전문가들이 ' . $main_keyword . '에 대해 다양한 조언을 제공하고 있습니다. 전문가들은 ' . $main_keyword . '의 기본 원리를 충실히 이해하는 것이 가장 중요하다고 강조합니다. 또한, 이론적인 지식뿐만 아니라 실제 경험을 통해 배우는 것이 중요하다고 말합니다. 실패를 두려워하지 말고 적극적으로 시도해보는 자세가 필요합니다.</p>';
        
        $content .= '<p>전문가들은 또한 ' . $main_keyword . '를 실천할 때 개인의 특성을 고려해야 한다고 조언합니다. 모든 사람에게 똑같이 적용되는 방법은 없으므로, 자신에게 맞는 방식을 찾는 것이 중요합니다. 다른 사람의 성공 사례를 참고하되, 맹목적으로 따라하기보다는 자신의 상황에 맞게 응용하는 지혜가 필요합니다.</p>';
        
        $content .= '<h2>결론</h2>';
        $content .= '<p>' . $main_keyword . '는 우리 삶의 질을 향상시키는 데 중요한 역할을 합니다. 이 글에서 다룬 내용들을 참고하여 자신만의 방법을 찾아가시기 바랍니다. ' . $main_keyword . '에 대한 이해를 높이고 꾸준히 실천한다면, 분명 긍정적인 변화를 경험하실 수 있을 것입니다. 시작이 반이라는 말처럼, 오늘부터라도 작은 변화를 시도해보시기 바랍니다.</p>';
        
        $post_data = array(
            'post_title' => $title,
            'post_content' => $content,
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type' => 'post',
        );
        
        wp_insert_post($post_data);
    }
}
