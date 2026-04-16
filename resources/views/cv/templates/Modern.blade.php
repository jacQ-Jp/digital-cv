@extends($layout ?? 'layouts.render')

@section('content')
@php
	$name = data_get($cv, 'personal_name') ?: (data_get($cv, 'user.name') ?? 'CV');
	$email = data_get($cv, 'personal_email') ?: (data_get($cv, 'user.email') ?? '');
	$title = data_get($cv, 'title');
	$summary = data_get($cv, 'summary');
	$phone = data_get($cv, 'personal_phone');
	$location = data_get($cv, 'personal_location');
	$linkedin = data_get($cv, 'personal_linkedin');
	$website = data_get($cv, 'personal_website');
	$thumbMode = ($layout === 'layouts.thumb');

	$photoPreview = data_get($cv, 'photo_preview_url');
	$photoPath = data_get($cv, 'photo_path');
	$photo = $photoPreview ?: ($photoPath ? asset('storage/'.$photoPath) : null);

	$accent = strtoupper((string) data_get($cv, 'accent_color', '#7C3AED'));
	$themes = [
		'#7C3AED' => ['accent' => '#7C3AED', 'deep' => '#4C1D95', 'soft' => '#EDE9FE'],
		'#0EA5A4' => ['accent' => '#0EA5A4', 'deep' => '#0F766E', 'soft' => '#CCFBF1'],
		'#3B82F6' => ['accent' => '#3B82F6', 'deep' => '#1D4ED8', 'soft' => '#DBEAFE'],
		'#EA580C' => ['accent' => '#EA580C', 'deep' => '#C2410C', 'soft' => '#FFEDD5'],
		'#334155' => ['accent' => '#334155', 'deep' => '#0F172A', 'soft' => '#E2E8F0'],
		'#166534' => ['accent' => '#166534', 'deep' => '#14532D', 'soft' => '#DCFCE7'],
		'#BE123C' => ['accent' => '#BE123C', 'deep' => '#9F1239', 'soft' => '#FFE4E6'],
	];
	$tone = $themes[$accent] ?? $themes['#7C3AED'];

	$experiences = collect(data_get($cv, 'experiences', []));
	$educations = collect(data_get($cv, 'educations', []));
	$skills = collect(data_get($cv, 'skills', []));

	$hasSummary = filled($summary);
	$hasExperiences = $experiences->isNotEmpty();
	$hasEducations = $educations->isNotEmpty();
	$hasSkills = $skills->isNotEmpty();
	$hasContact = filled($email) || filled($phone) || filled($linkedin) || filled($website);
	$hasMainColumn = $hasSummary || $hasExperiences;
	$hasSideColumn = $hasEducations || $hasSkills || $hasContact;

	$placeholderFlags = collect(data_get($cv, 'preview_placeholder_flags', []));
	$isPlaceholder = fn (string $key): bool => (bool) $placeholderFlags->get($key, false);
	$itemPlaceholder = fn ($item, string $field): bool => (bool) data_get($item, '_placeholder.'.$field, false);
@endphp

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Manrope:wght@400;600;700&display=swap');

.cv-paper {
	--ac: {{ $tone['accent'] }};
	--ac-deep: {{ $tone['deep'] }};
	--ac-soft: {{ $tone['soft'] }};
}

.cv-modern {
	font-family: 'Manrope', sans-serif;
	color: #0f172a;
	font-size: 13px;
	line-height: 1.65;
}

.cv-modern .cv-placeholder { opacity: .54; font-style: italic; }

.cv-modern-head {
	border-radius: 14px;
	padding: 18px 20px;
	background: linear-gradient(135deg, var(--ac-deep), var(--ac));
	color: #fff;
	display: grid;
	grid-template-columns: 1fr auto;
	gap: 14px;
}

.cv-modern-name {
	margin: 0;
	font-family: 'Poppins', sans-serif;
	font-size: 30px;
	font-weight: 800;
	line-height: 1.08;
}

.cv-modern-role {
	margin-top: 6px;
	font-size: 12px;
	letter-spacing: .12em;
	text-transform: uppercase;
	font-weight: 700;
	color: #e2e8f0;
}

.cv-modern-meta {
	display: flex;
	flex-wrap: wrap;
	gap: 6px 14px;
	margin-top: 10px;
	font-size: 11.5px;
	color: #e2e8f0;
}

.cv-modern-photo {
	width: 88px;
	height: 88px;
	border-radius: 16px;
	border: 2px solid #ffffff66;
	object-fit: cover;
	background: #ffffff22;
}

.cv-modern-main {
	margin-top: 14px;
	display: grid;
	grid-template-columns: 1.65fr .95fr;
	gap: 14px;
}

.cv-modern-main-single {
	grid-template-columns: 1fr;
}

.cv-block {
	border: 1px solid #e2e8f0;
	border-radius: 12px;
	background: #fff;
	padding: 12px 13px;
	margin-bottom: 12px;
}

.cv-block-title {
	margin: 0 0 8px;
	font-family: 'Poppins', sans-serif;
	color: var(--ac-deep);
	font-size: 11px;
	text-transform: uppercase;
	letter-spacing: .14em;
	font-weight: 700;
}

.cv-summary {
	margin: 0;
	font-size: 12.5px;
	line-height: 1.7;
	color: #334155;
}

.cv-item {
	border-left: 3px solid var(--ac-soft);
	padding-left: 10px;
	margin-bottom: 11px;
}

.cv-item-row {
	display: flex;
	justify-content: space-between;
	gap: 10px;
	align-items: baseline;
}

.cv-item-role {
	margin: 0;
	font-family: 'Poppins', sans-serif;
	font-size: 14px;
	font-weight: 700;
}

.cv-item-date {
	font-size: 10.5px;
	color: var(--ac-deep);
	background: var(--ac-soft);
	padding: 3px 7px;
	border-radius: 999px;
	font-weight: 700;
}

.cv-item-sub {
	font-size: 12px;
	color: #475569;
	margin-top: 2px;
}

.cv-item-desc {
	font-size: 11.8px;
	color: #334155;
	margin-top: 6px;
	line-height: 1.65;
}

.cv-chip-wrap { display: flex; flex-wrap: wrap; gap: 7px; }

.cv-chip {
	font-size: 11px;
	font-weight: 700;
	color: var(--ac-deep);
	background: var(--ac-soft);
	border: 1px solid #dbeafe;
	border-radius: 999px;
	padding: 4px 9px;
}

.cv-contact-row {
	margin-bottom: 7px;
	font-size: 12px;
	color: #334155;
	word-break: break-all;
}

.cv-contact-row b { color: var(--ac-deep); }

@media (max-width: 760px) {
	.cv-modern-head { grid-template-columns: 1fr; }
	.cv-modern-main { grid-template-columns: 1fr; }
}
</style>

<div class="cv-modern">
	<header class="cv-modern-head">
		<div>
			<h1 class="cv-modern-name {{ $isPlaceholder('personal_name') ? 'cv-placeholder' : '' }}">{{ $thumbMode ? Str::limit($name, 24) : $name }}</h1>
			@if($title)<div class="cv-modern-role {{ $isPlaceholder('title') ? 'cv-placeholder' : '' }}">{{ $title }}</div>@endif
			<div class="cv-modern-meta">
				@if($email)<span class="{{ $isPlaceholder('personal_email') ? 'cv-placeholder' : '' }}">{{ $email }}</span>@endif
				@if($phone)<span>{{ $phone }}</span>@endif
				@if($location)<span>{{ $location }}</span>@endif
			</div>
		</div>
		@if($photo)<img src="{{ $photo }}" alt="Photo" class="cv-modern-photo">@endif
	</header>

	<div class="cv-modern-main {{ (!$hasMainColumn || !$hasSideColumn) ? 'cv-modern-main-single' : '' }}">
		@if($hasMainColumn)
		<div>
			@if($hasSummary)
				<section class="cv-block">
					<h2 class="cv-block-title">Professional Summary</h2>
					<p class="cv-summary {{ $isPlaceholder('summary') ? 'cv-placeholder' : '' }}">{{ $thumbMode ? Str::limit($summary, 220) : $summary }}</p>
				</section>
			@endif

			@if($hasExperiences)
				<section class="cv-block">
					<h2 class="cv-block-title">Experience</h2>
					@foreach(($thumbMode ? $experiences->take(3) : $experiences) as $exp)
						<article class="cv-item">
							<div class="cv-item-row">
								<p class="cv-item-role {{ $itemPlaceholder($exp, 'position') ? 'cv-placeholder' : '' }}">{{ data_get($exp, 'position') }}</p>
								<span class="cv-item-date {{ ($itemPlaceholder($exp, 'start_date') || $itemPlaceholder($exp, 'end_date')) ? 'cv-placeholder' : '' }}">{{ data_get($exp, 'start_date') }}{{ data_get($exp, 'end_date') ? ' - '.data_get($exp, 'end_date') : ' - Present' }}</span>
							</div>
							<div class="cv-item-sub {{ $itemPlaceholder($exp, 'company') ? 'cv-placeholder' : '' }}">{{ data_get($exp, 'company') }}</div>
							@if(data_get($exp, 'description'))
								<div class="cv-item-desc {{ $itemPlaceholder($exp, 'description') ? 'cv-placeholder' : '' }}">{{ $thumbMode ? Str::limit((string) data_get($exp, 'description'), 170) : data_get($exp, 'description') }}</div>
							@endif
						</article>
					@endforeach
				</section>
			@endif
		</div>
		@endif

		@if($hasSideColumn)
		<aside>
			@if($hasEducations)
				<section class="cv-block">
					<h2 class="cv-block-title">Education</h2>
					@foreach($educations as $edu)
						<article class="cv-item">
							<div class="cv-item-role {{ $itemPlaceholder($edu, 'school') ? 'cv-placeholder' : '' }}">{{ data_get($edu, 'school') }}</div>
							<div class="cv-item-sub {{ $itemPlaceholder($edu, 'degree') ? 'cv-placeholder' : '' }}">{{ data_get($edu, 'degree') }}</div>
							<div class="cv-item-date {{ $itemPlaceholder($edu, 'year') ? 'cv-placeholder' : '' }}" style="display:inline-flex;margin-top:6px;">{{ data_get($edu, 'year') }}</div>
						</article>
					@endforeach
				</section>
			@endif

			@if($hasSkills)
				<section class="cv-block">
					<h2 class="cv-block-title">Skills</h2>
					<div class="cv-chip-wrap">
						@foreach($skills as $skill)
							<span class="cv-chip {{ $itemPlaceholder($skill, 'name') ? 'cv-placeholder' : '' }}">{{ data_get($skill, 'name') }}</span>
						@endforeach
					</div>
				</section>
			@endif

			@if($hasContact)
				<section class="cv-block">
					<h2 class="cv-block-title">Contact</h2>
					@if($email)<div class="cv-contact-row {{ $isPlaceholder('personal_email') ? 'cv-placeholder' : '' }}"><b>Email:</b> {{ $email }}</div>@endif
					@if($phone)<div class="cv-contact-row"><b>Phone:</b> {{ $phone }}</div>@endif
					@if($linkedin)<div class="cv-contact-row"><b>LinkedIn:</b> {{ $linkedin }}</div>@endif
					@if($website)<div class="cv-contact-row"><b>Web:</b> {{ $website }}</div>@endif
				</section>
			@endif
		</aside>
		@endif
	</div>
</div>
@endsection
